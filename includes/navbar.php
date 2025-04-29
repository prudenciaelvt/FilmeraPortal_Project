<!-- navbar.php -->
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        margin: 0;
    }

    .navbar {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color:black;
        padding: 1rem 2rem;
        font-family: sans-serif;
    }

    .navbar .logo {
        display: flex;
        align-items: center;
        font-size: 1.2rem;
        font-weight: bold;
        color: white;
        text-decoration: none;
    }

    .navbar .logo img {
        width: 30px;
        height: 30px;
        margin-right: 10px;
    }

    .navbar .menu {
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    .navbar .menu a {
        color: white;
        text-decoration: none;
        font-weight: 500;

    }

    .navbar .menu a:hover {
        text-decoration: underline;
    }

    .navbar .btn-login {
        padding: 8px 18px;
        background-color: #E94057;
        color: white;
        border-radius: 20px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .navbar .btn-login:hover {
        background-color: #c9304c;
    }
</style>

<nav class="navbar">
    <a href="index.php" class="logo">
        <img src="../assets/picture/logo_filmeraportal.png" alt="Logo">
        FILMERAPORTAL
    </a>
</nav>
