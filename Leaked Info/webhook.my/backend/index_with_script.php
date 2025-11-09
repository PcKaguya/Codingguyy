<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discord Webhook Protector</title>
    <meta property="og:title" content="Discord Webhook Protector">
    <meta property="og:description" content="This is a simple Discord webhook protector (backend) preventing reversers from spamming & deleting webhooks. This forwards the whole content to your real webhook">
    <meta property="og:image" content="https://cdn-icons-png.flaticon.com/512/2344/2344282.png">
    <meta name="theme-color" content="#eb34e5">
    <meta property="og:site_name" content="Discord: malwaredeveloper">
    <meta property="og:url" content="https://webhook.my">
    <meta property="og:type" content="website">
    <link rel="icon" type="image/x-icon" href="https://cdn-icons-png.flaticon.com/512/2344/2344282.png">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #0B0E14;
            min-height: 100vh;
            font-family: system-ui, -apple-system, sans-serif;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .banner {
            background: rgba(30, 31, 41, 0.95);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            text-align: center;
            border: 1px solid rgba(88, 101, 242, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .banner:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(88, 101, 242, 0.2);
        }

        .banner h1 {
            font-size: 1rem;
            color: #fff;
        }

        .banner a {
            color: #5865F2;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .banner a:hover {
            color: #7289DA;
        }

        .container {
            background: #1E1F29;
            padding: 2rem;
            border-radius: 16px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1.title {
            color: #5865F2;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        form {
            width: 100%;
        }

        label {
            color: #B9BBBE;
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-align: center;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(88, 101, 242, 0.2);
            border-radius: 8px;
            color: #FFFFFF;
            margin-bottom: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #5865F2;
            box-shadow: 0 0 0 3px rgba(88, 101, 242, 0.25);
        }

        .button {
            width: 100%;
            padding: 0.75rem;
            background: #5865F2;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .button:hover {
            background: #4752C4;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(88, 101, 242, 0.4);
        }

        .button:active {
            transform: translateY(0);
        }







        .copy-btn {
            width: 100%;
            padding: 0.75rem;
            background: #5865F2;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .copy-btn:hover {
            background: #4752C4;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(88, 101, 242, 0.4);
        }

        .copy-btn:active {
            transform: translateY(0);
        }
				

    </style>
</head>
<body>
    <div class="banner">
        <h1>Sourcecode & database export is for sale <a href="https://t.me/reversedbytes" target="_blank">DM ME HERE</a></h1>
    </div>

    <div class="banner">
        <h1>Our backup links can be found <a href="https://webhook.my/info" target="_blank">HERE</a></h1>
    </div>

    <div class="container">
        <h1 class="title">Discord Webhook Protector</h1>
        <form id="webhook-form">
		
			<label for="webhook">Enter Discord Webhook URL:</label>
            <input type="text" id="webhook" name="webhook" placeholder="https://discord.com/api/webhooks/..." required>
            <button type="submit" class="button">Protect Webhook</button>
			
        </form>
		
		
        <div id="copy-btn-container"></div>
    </div>

    <script>
        function copyToClipboard(url) {
            var copyText = document.createElement("textarea");
            copyText.value = url;
            document.body.appendChild(copyText);
            copyText.select();
            document.execCommand("copy");
            document.body.removeChild(copyText);
            alert("Protected Webhook URL Copied: " + copyText.value);
        }

        document.querySelector("#webhook-form").addEventListener("submit", function(event) {
            event.preventDefault();
            var formData = new FormData(event.target);
            fetch("/create", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    var btnContainer = document.getElementById("copy-btn-container");
                    btnContainer.innerHTML = '';
                    var btn = document.createElement("button");
                    btn.classList.add("copy-btn");
                    btn.innerText = "Copy Protected Webhook URL";
                    btn.onclick = function() {
                        copyToClipboard(data.protected_url);
                    };
                    btnContainer.appendChild(btn);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
