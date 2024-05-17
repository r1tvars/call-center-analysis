<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple CSS Cards</title>

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
                if (card.getAttribute('status') == 'in-processing') {
                    updateCard(card.getAttribute('id'));
                }
            });
        }, 20000);
    </script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: center;
        }

        .card {
            /* max-width: 300px; */
            width: 95%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-1px);
        }



        .card .content {
            padding: 16px;
        }

        .card .content .title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .card .content p {
            color: #555;
            font-size: 1rem;
            margin: 0;
        }

        .loader {
            border: 4px solid #f3f3f3; /* Light grey */
            border-top: 4px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
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