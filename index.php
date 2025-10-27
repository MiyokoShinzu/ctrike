<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C-Trike Predictive Maintenance</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        :root {
            --primary: rgb(174, 14, 14);
            --primary-light: rgb(220, 60, 60);
            --text: #222;
            --background: #fff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #fff;
        }

        /* Header */
        header {
            width: 100%;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        header img {
            height: 60px;
        }

        header button {
            background: var(--primary);
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.6rem 1.4rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        header button:hover {
            background: var(--primary-light);
        }

        /* Main Section */
        main {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 6rem 1rem 2rem;
         
        }




        h1 {
            font-size: 2.3rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        p {
            font-size: 1.05rem;
            color: #444;
            max-width: 600px;
            margin-bottom: 2rem;
        }

        .cta-btn {
            background: var(--bs-primary);
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.9rem 2.4rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s ease;

        }

        .cta-btn:hover {
            background: var(--bs-success);

        }

        /* Footer */
        footer {
            text-align: center;
            padding: 1rem;
            font-size: 0.9rem;
            color: #777;
            background: #fff;
            border-top: 1px solid #eee;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }

            p {
                font-size: 1rem;
            }


        }
    </style>
</head>

<body>

    <header>
        <img src="./assets/emob_logo_f.png" alt="C-Trike Logo">
        <button onclick="window.location.href='login/'" class="btn btn-outline-success ">Login</button>
    </header>

    <main>
        <div class="row">
            <div class="col-lg-12 mb-2">
                <h1 class="text-success">C-Trike Predictive Maintenance System</h1>
            </div>
            <div class="col-lg-12 mb-2">
                <img src="./assets/emob_logo_f.png" alt="C-Trike Logo" style="max-height: 300px; max-width: auto;">
            </div>
            <div class="col-lg-7 mx-auto mt-4 mb-2">
                <h4 class="mb-2">Empowering electric mobility with intelligent performance tracking and predictive maintenance solutions.</h4>
                <button class="btn btn-success btn-lg mt-3" onclick="window.location.href='login/'">Get Started</button>
            </div>
        </div>



    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> C-Trike Predictive Maintenance System. All rights reserved.
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</body>

</html>