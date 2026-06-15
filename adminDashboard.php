
<?php
session_start();
include("db.php");


if (!isset($_SESSION['ID'])) {
    header("Location: index.php");
    exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing:
                border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #0E1422;
            color: white;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 200px;
            background: linear-gradient(to bottom, #0f1d3c, #09142b);
            position: relative;
            padding-top: 20px;
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo img {
            width: 90px;
        }

        .logo h2 {
            color: #ffce1b;
            margin-top: 5px;
            letter-spacing: 2px;
        }

        .menu {
            list-style: none;
        }

        .menu li {
            padding: 18px 30px;
            display: flex;
            align-items:
                center;
            gap: 15px;
            cursor: pointer;
            transition: 0.3s;
            font-size: 18px;
        }

        .menu li:hover {

            background: #17396f;
        }

        .menu .active {

            background: #1f4d8d;
        }

        a {
            text-decoration: none;
            color: white;
        }

        .logout {
            position: absolute;
            bottom: 30px;
            width: 100%;
            cursor: pointer;
        }

        .main {
            flex: 1;
            padding: 25px;
            background: linear-gradient(to right, #050b18, #071b4a, #050b18);
        }

        .title {
            font-size: 50px;
            font-weight: 300;
            margin-bottom: 40px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: 90px 90px 300px;
            gap: 20px;
        }

        .card {
            background: #0b2458;
            border-radius: 15px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card img {
            width: 45px;
        }

        .card h3 {
            font-size: 13px;
            font-weight: 400;
        }

        .card h1 {
            font-size: 28px;
        }

        .card1 {
            grid-column: 1;
        }

        .card2 {
            grid-column: 2;
        }

        .card3 {
            grid-column: 3;
        }

        .card4 {
            grid-column: 1;
            grid-row: 2;
        }

        .status-card {
            grid-column: 2 / 4;
            grid-row: 2 / 4;
            background: #0b2458;
            border-radius: 25px;
            padding: 20px;
        }

        .status-card h3 {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            display: table;
            width: 100%;
            border-spacing: 0;
            border: 1px solid white;
            overflow: hidden;
            border-radius: 12px;
            background: #0b1f4d;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, .15);
            font-size: 15px;
        }

        .chart-box {
            grid-column: 1 / 2;
            grid-row: 3;
            background: #0b2458;
            border-radius: 25px;
            padding: 15px;
        }

        .chart-box h3 {
            font-size: 15px;
            margin-bottom: 15px;
        }

        .circle-chart {
            width: 120px;
            height: 120px;
            margin: auto;
            position: relative;
        }

        .c1,
        .c2,
        .c3,
        .c4 {
            position: absolute;
            border-radius: 50%;
            border: 6px solid transparent;
        }

        .c1 {
            width: 140px;
            height: 140px;
            border-top-color: #1d8dff;
            border-right-color: #1d8dff;
        }

        .c2 {
            width: 115px;
            height: 115px;
            top: 12px;
            left: 12px;
            border-top-color: #ff4d78;
            border-right-color: #ff4d78;
        }

        .c3 {
            width: 90px;
            height: 90px;
            top: 25px;
            left: 25px;
            border-top-color: #00d7a7;
            border-right-color: #00d7a7;
        }

        .c4 {
            width: 65px;
            height: 65px;
            top: 37px;
            left: 37px;
            border-top-color: #c44fff;
            border-right-color: #c44fff;
        }

        .legend {
            margin-top: 30px;
            font-size: 12px;
        }

        .legend span {
            width: 10px;
            height: 10px;
            display: inline-block;
            margin-right: 8px;
        }

        .blue {
            background: #1e88ff;
        }

        .red {
            background: #ff4d78;
        }

        .green {
            background: #00d7a7;
        }

        .purple {
            background: #c44fff;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-overlay.show {
            display: flex;
        }

        .logout-card {
            background: #131f38;
            width: 500px;
            border-radius: 12px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .logout-title {
            font-size: 26px;
            color: white;
            font-weight: 500;
            margin-bottom: 30px;
        }

        .logout-buttons {
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        .btn-logout-cancel {
            background: #5e5b5a;
            color: white;
            font-size: 18px;
            padding: 10px 35px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            min-width: 130px;
        }

        .btn-logout-confirm {
            background: #194d2b;
            color: white;
            font-size: 18px;
            padding: 10px 35px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            min-width: 130px;
        }

        .highlight {
            color: #ffce1b;
        }
    </style>
</head>

<body>



    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <img src="Logo.png">
                <h2>SALIN<span class="highlight">GO</span></h2>
            </div>
            <ul class="menu">
                <li class="active"><i class="fa-solid fa-table-cells-large"></i> Dashboard</li>
                <a href="languageManagement.php">
                    <li><i class="fa-solid fa-globe"></i> Language</li>
                </a>
                <a href="deviceManagement.php">
                 <li><i class="fa-solid fa-desktop"></i> Device</li>
                  </a>
                <li><i class="fa-solid fa-rotate"></i> Update</li>
            </ul>
            <div class="menu logout" id="sidebarLogoutLink">
                <li><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</li>
            </div>
        </div>
        <div class="main">
            <h1 class="title">Dashboard</h1>
            <div class="dashboard-grid">
                <div class="card card1"><img src="Logo.png">
                    <div>
                        <h3>Active Device</h3>
                        <h1>162</h1>
                    </div>
                </div>
                <div class="card card2"><img src="Logo.png">
                    <div>
                        <h3>Active Language</h3>
                        <h1>3</h1>
                    </div>
                </div>
                <div class="card card3"><img src="Logo.png">
                    <div>
                        <h3>Total Translation</h3>
                        <h1>167,571</h1>
                    </div>
                </div>
                <div class="card card4"><img src="Logo.png">
                    <div>
                        <h3>Latest Version</h3>
                        <h1>V1.0.0.4</h1>
                    </div>
                </div>
                <div class="chart-box">
                    <h3>Most Used Language</h3>
                    <div class="circle-chart">
                        <div class="c1"></div>
                        <div class="c2"></div>
                        <div class="c3"></div>
                        <div class="c4"></div>
                    </div>
                    <div class="legend">
                        <p><span class="blue"></span> Kapampangan</p>
                        <p><span class="red"></span> English</p>
                        <p><span class="green"></span> Tagalog</p>
                        <p><span class="purple"></span> Others</p>
                    </div>
                </div>
                <div class="status-card">
                    <h3>Device Status</h3>
                    <table>
                        <tr>
                            <th>Device ID</th>
                            <th>Connectivity</th>
                            <th>Version</th>
                            <th>Status</th>
                        </tr>
                        <tr>
                            <td>D00180</td>
                            <td style="color:#00ff66;">Online</td>
                            <td>V1.0.0.4</td>
                            <td>Active</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="logoutModal">
        <div class="logout-card">
            <div class="logout-title">Are you sure you want to log out?</div>
            <div class="logout-buttons">
                <button class="btn-logout-cancel" id="closeLogoutCancel">Cancel</button>
                <button class="btn-logout-confirm" id="confirmLogout">Logout</button>
            </div>
        </div>
    </div>

    <script>


        const sidebarLogoutLink = document.getElementById('sidebarLogoutLink');
        const logoutModal = document.getElementById('logoutModal');
        const closeLogoutCancel = document.getElementById('closeLogoutCancel');
        const confirmLogout = document.getElementById('confirmLogout');

        sidebarLogoutLink.addEventListener('click', () => {
            logoutModal.classList.add('show');
        });

        closeLogoutCancel.addEventListener('click', () => {
            logoutModal.classList.remove('show');
        });

        confirmLogout.addEventListener('click', () => {
            window.location.href = 'adminDashboard.php?action=logout';
        });

        window.addEventListener('click', (e) => {
            if (e.target === logoutModal) { logoutModal.classList.remove('show'); }
        });

          
    



        
    </script>


</body>

</html>