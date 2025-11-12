import asyncio

import discord
import requests
from discord.ext import commands

BOT_TOKEN = "DISCORD-TOKEN"

intents = discord.Intents.all()


bot = commands.Bot(command_prefix=".", intents=intents)


@bot.event
async def on_ready():
    print(f"Logged in as {bot.user.name}")


@bot.command(name="blockpost")
async def blockpost(ctx, uniqueid: str):
    r = requests.get(f"http://webhook.my/blockpost?uniqueid={uniqueid}")
    await ctx.send(r.text, delete_after=60)


@bot.command(name="blockcreate")
async def blockpost(ctx, uniqueid: str):
    r = requests.get(f"http://webhook.my/blockcreate?uniqueid={uniqueid}")
    await ctx.send(r.text, delete_after=60)


@bot.command(name="delete")
async def delete(ctx, uniqueid: str):
    r = requests.delete(f"http://webhook.my/delete?uniqueid={uniqueid}")
    await ctx.send(r.text, delete_after=60)


bot.run(BOT_TOKEN)
