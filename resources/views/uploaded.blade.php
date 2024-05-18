<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Call Center Audio Post Analysis</title>

    <script>
        async function updateCard(cardId) {
            try {
                const response = await fetch(`/card/${cardId}`);
                if (response.ok) {
                    const html = await response.text();
                    const found = html.search('succeeded');
                    if (html.trim() && html.includes('succeeded')) {
                        console.log('asdf');
                        const cardElement = document.getElementsByClassName('card')[cardId-1];

                            cardElement.outerHTML = html;

                    }
                } else if (response.status === 204) {
                    console.log('No content to update');
                }
            } catch (error) {
                console.error('Error fetching card:', error);
            }
        }

        setInterval(() => {
            document.querySelectorAll('.card').forEach(card => {
                if (card.getAttribute('status') == 'in-processing' || card.getAttribute('status') == 'failed') {
                    updateCard(card.getAttribute('id'));
                }
            });
        }, 1000);
    </script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            /* height: 100vh; */
            margin: 0;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            width: 78%;
            padding: 100px 0;
        }

        .card {
            /* max-width: 300px; */
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-1px);
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .card .content {
            display: flex;
            align-items: center;
            padding: 16px;
            gap: 5px;
        }

        .card .content .title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .card .content p {
            color: #555;
            font-size: 14px;
            margin: 0;
        }
        .card .content .content-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .card .content .content-info h3 {
           margin: 0;
        }
        .loader {
            border: 4px solid #f3f3f3; /* Light grey */
            border-top: 4px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
            margin-left: auto;
        }

        .status {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            width: fit-content;
            font-size: 14px;
        }

        .status.succeeded {
            background-color: green;
        }

        .status.running {
            background-color: orange;
        }

        .status.failed {
            background-color: red;
        }

        @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    @include('cards', ['cards' => $records])
</body>
</html>
