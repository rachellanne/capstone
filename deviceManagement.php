<?php
include("db.php");


$version_filter = isset($_GET['version']) ? $_GET['version'] : '';

$sql = "SELECT * FROM deviceManagement";
if (!empty($version_filter)) {
    $sql .= " WHERE VersionCode = '" . mysqli_real_escape_string($conn, $version_filter) . "'";
}

$result = mysqli_query($conn, $sql);
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$connectivity_filter = isset($_GET['connectivity']) ? $_GET['connectivity'] : '';

$sql = "SELECT * FROM deviceManagement WHERE 1=1";

if (!empty($version_filter)) {
    $sql .= " AND VersionCode='" . mysqli_real_escape_string($conn, $version_filter) . "'";
}

if (!empty($status_filter)) {
    $sql .= " AND Status='" . mysqli_real_escape_string($conn, $status_filter) . "'";
}

if (!empty($connectivity_filter)) {
    $sql .= " AND Connectivity='" . mysqli_real_escape_string($conn, $connectivity_filter) . "'";
}
if (!empty($search)) {

    $search_safe = mysqli_real_escape_string($conn, $search);

    $sql .= " AND (
        CONCAT('D',LPAD(ID,5,'0')) LIKE '%$search_safe%'
        OR VersionCode LIKE '%$search_safe%'
        OR Status LIKE '%$search_safe%'
        OR Connectivity LIKE '%$search_safe%'
    )";

}

$result = mysqli_query($conn, $sql);

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM deviceManagement");
$total_devices = mysqli_fetch_assoc($total_query)['total'];

$online_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM deviceManagement WHERE Connectivity = 'Online'");
$online_devices = mysqli_fetch_assoc($online_query)['total'];
?>

<!DOCTYPE html>
<html lang="en">



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        .sidebar a {
            text-decoration: none;
            color: white;
        }

        .menu li {
            padding: 18px 30px;
            display: flex;
            align-items: center;
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

        .logout {
            position: absolute;
            bottom: 30px;
            width: 100%;
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

        .topBar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            width: 100%;
        }

        .tabs-container {
            margin-bottom: 15px;
            position: relative;
        }

        .tabs {
            display: flex;
            gap: 30px;
            font-weight: 600;
            border-bottom: 2px solid rgba(255, 255, 255, 0.15);
            padding-bottom: 8px;
        }

        .tab-item {
            cursor: pointer;
            color: rgba(255, 255, 255, 0.6);
            transition: 0.3s;
            font-size: 16px;
            position: relative;
        }

        .tab-item.active-tab {
            color: white;
        }


        .buttons {
            display: flex;
            gap: 15px;
        }

        .buttons button {
            padding: 10px 20px;
            border-radius: 20px;
            border: 1px solid white;
            background: transparent;
            color: white;
            cursor: pointer;
            transition: 0.3s;
        }

        .buttons button:hover {
            background: #1f4d8d;
        }

        .buttons .archive-btn {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 5px;
            padding: 6px 15px;
        }

        select {
            background: #0b1f4d;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
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
            color: rgba(255, 255, 255, 0.7);
        }

        .card h1 {
            font-size: 28px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            overflow: hidden;
            background: #0b1f4d;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            font-size: 15px;
        }

        th {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.02);
        }

        tr:last-child td {
            border-bottom: none;
        }

        thead tr:first-child th:first-child,
        thead tr:first-child td:first-child {
            border-top-left-radius: 12px;
        }

        thead tr:first-child th:last-child,
        thead tr:first-child td:last-child {
            border-top-right-radius: 12px;
        }

        tbody tr:last-child td:first-child {
            border-bottom-left-radius: 12px;
        }

        tbody tr:last-child td:last-child {
            border-bottom-right-radius: 12px;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .file-badge {
            background: #d9d9d9;
            color: black;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-active {
            background: #116149;
            padding: 5px 12px;
            border-radius: 4px;
            color: #57ffb2;
            font-size: 13px;
        }

        .status-inactive {
            background: gray;
            padding: 5px 12px;
            border-radius: 4px;
            color: white;
            font-size: 13px;
        }

        .action i {
            margin: 0 8px;
            cursor: pointer;
            opacity: 0.8;
            transition: 0.2s;
        }

        .action i:hover {
            opacity: 1;
            color: #ffce1b;
        }

        .checkbox {
            width: 17px;
            height: 17px;
            cursor: pointer;
        }

        .highlight {
            color: white;
        }

        .ui-section {
            display: none;
        }

        .ui-section.active-section {
            display: block;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            background: #111e38;
            width: 540px;
            border-radius: 16px;
            padding: 30px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .modal-close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #a92323;
            color: white;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: background 0.2s;
        }

        .modal-close-btn:hover {
            background: #c93434;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 35px;
        }

        .modal-message {
            font-size: 18px;
            margin-bottom: 35px;
            text-align: center;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        .modal-btn {
            padding: 10px 35px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            font-weight: 500;
            min-width: 110px;
        }

        .btn-cancel {
            background: #5a5a5a;
            color: white;
        }

        .btn-yes {
            background: #164f2b;
            color: white;
        }

        .modal-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 45px;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .form-group input[type="text"] {
            width: 100%;
            background: #161f33;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            padding: 10px;
            color: white;
            outline: none;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, .5);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal-content {
            width: 600px;
            background: #12244d;
            border-radius: 25px;
            padding: 40px;
            position: relative;
            border: 1px solid rgba(255, 255, 255, .15);
        }

        .modal-content h1 {
            font-size: 45px;
            margin-bottom: 40px;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #d53c3c;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 25px;
        }

        .modal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .modal-grid h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .modal-grid p {
            color: #d0d0d0;
            font-size: 18px;
        }

        .status-box {
            text-align: center;
            margin-top: 35px;
        }

        .status-box h2 {
            margin-bottom: 10px;
        }

        .back-btn {
            display: block;
            margin: 30px auto 0;
            padding: 10px 50px;
            border: none;
            border-radius: 10px;
            background: #666;
            color: white;
            font-size: 22px;
            cursor: pointer;
        }

        .modal-grid input[type="text"] {
            width: 100%;
            padding: 10px;
            background: #161f33;
            color: white;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 8px;
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
            transition: background 0.2s;
        }

        .btn-logout-cancel:hover {
            background: #6e6b6a;
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
            transition: background 0.2s;
        }

        .btn-logout-confirm:hover {
            background: #206137;
        }

        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
        }

        .filter-group {
            display: flex;
            gap: 10px;
        }

        .filter-select {
            background: #0b1f4d;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-container {
            display: flex;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            overflow: hidden;
        }

        .search-container input {
            background: transparent;
            border: none;
            padding: 8px 15px;
            color: white;
            outline: none;
        }

        .search-container button {
            background: #0b1f4d;
            border: none;
            color: white;
            padding: 8px 15px;
            cursor: pointer;
            border-left: 1px solid rgba(255, 255, 255, 0.2);
        }

        .search-wrapper {
            position: relative;
        }

        .recent-list {

            position: absolute;
            top: 45px;
            left: 0;
            width: 100%;
            background: #13244d;
            border-radius: 8px;
            border: 1px solid yellow;
            display: none;
            z-index: 999;
            overflow: hidden;

        }

        .recent-item {

            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid rgba(255, 255, 255, .1);

        }

        .recent-item:hover {

            background: #1b3572;

        }

        .status-maintenance {
            background: #827B2F;

            padding: 5px 12px;
            border-radius: 4px;
            font-size: 13px;

        }
    </style>
</head>

<body>

    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <img src="Logo.png" alt="Logo">
                <h2>SALIN<span class="highlight">GO</span></h2>
            </div>
            <ul class="menu">
                <a href="adminDashboard.php">
                    <li><i class="fa-solid fa-table-cells-large"></i> Dashboard</li>
                </a>
                <a href="languageManagement.php">
                    <li><i class="fa-solid fa-globe"></i> Language</li>
                </a>
                <a href="deviceManagement.php">
                    <li class="active"><i class="fa-solid fa-desktop"></i> Device</li>
                </a>
                <a href="">
                    <li><i class="fa-solid fa-rotate"></i> Update</li>
                </a>
            </ul>
        </div>

        <div class="main">
            <h1 class="title">Device Management</h1>
            <div class="dashboard-grid">
                <div class="card">
                    <img src="Logo.png" alt="Icon">
                    <div>
                        <h3>Total Devices</h3>
                        <h1><?php echo $total_devices; ?></h1>
                    </div>
                </div>
                <div class="card">
                    <img src="Logo.png" alt="Icon">
                    <div>
                        <h3>Online Devices</h3>
                        <h1><?php echo $online_devices . "/" . $total_devices; ?></h1>
                    </div>

                </div>
                <div class="card">
                    <img src="Logo.png" alt="Icon">
                    <div>
                        <h3>Average Latency</h3>
                        <h1>42ms</h1>
                    </div>
                </div>
            </div>


            <div class="filter-bar">
                <div class="filter-group">

                    <select class="filter-select" id="version">
                        <option value="">Version Code</option>
                        <option value="v1.0.0.1" <?php if ($version_filter == "v1.0.0.1")
                            echo "selected"; ?>>V1.0.0.1
                        </option>
                        <option value="v1.0.0.2" <?php if ($version_filter == "v1.0.0.2")
                            echo "selected"; ?>>V1.0.0.2
                        </option>
                        <option value="v1.0.0.3" <?php if ($version_filter == "v1.0.0.3")
                            echo "selected"; ?>>V1.0.0.3
                        </option>
                        <option value="v1.0.0.4" <?php if ($version_filter == "v1.0.0.4")
                            echo "selected"; ?>>V1.0.0.4
                        </option>
                    </select>


                    <select class="filter-select" id="status">
                        <option value="">Status</option>
                        <option value="Active" <?php if ($status_filter == "Active")
                            echo "selected"; ?>>Active</option>
                        <option value="Inactive" <?php if ($status_filter == "Inactive")
                            echo "selected"; ?>>Inactive
                        </option>
                        <option value="Maintenance" <?php if ($status_filter == "Maintenance")
                            echo "selected"; ?>>Maintenance
                        </option>
                    </select>

                    <select class="filter-select" id="connectivity">
                        <option value="">Connectivity</option>
                        <option value="Online" <?php if ($connectivity_filter == "Online")
                            echo "selected"; ?>>Online
                        </option>
                        <option value="Offline" <?php if ($connectivity_filter == "Offline")
                            echo "selected"; ?>>Offline
                        </option>
                    </select>

                </div>

                <div class="search-wrapper">

                    <div class="search-container">

                        <input id="searchBox" type="text" placeholder="Search..."
                            value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">

                        <button id="searchBtn">
                            <i class="fa fa-search"></i>
                        </button>

                    </div>

                    <div id="recentList" class="recent-list"></div>

                </div>

            </div>


            <table>
                <thead>
                    <tr>
                        <td><input type="checkbox" id="selectAll" class="checkbox"></td>
                        <th>Device ID</th>
                        <th>Version Code</th>
                        <th>Status</th>
                        <th>Connectivity</th>
                        <th>Last Active</th>
                        <th>Action</th>
                    </tr>

                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $formatted_id = "D" . str_pad($row['ID'], 5, "0", STR_PAD_LEFT);
                            ?>
                            <tr>
                                <td><input type="checkbox" class="checkbox row-checkbox"></td>
                                <td><?php echo $formatted_id; ?></td>
                                <td><?php echo $row['VersionCode']; ?></td>

                                <td>

                                    <?php

                                    if ($row['Status'] == "Active") {
                                        $class = "status-active";
                                    } elseif ($row['Status'] == "Maintenance") {
                                        $class = "status-maintenance";
                                    } else {
                                        $class = "status-inactive";
                                    }

                                    ?>

                                    <span class="<?php echo $class; ?>">
                                        ● <?php echo $row['Status']; ?>
                                    </span>

                                </td>

                                <td><span
                                        class="<?php echo ($row['Connectivity'] == 'Online') ? 'status-active' : 'status-inactive'; ?>">●
                                        <?php echo $row['Connectivity']; ?>
                                    </span></td>

                                <td><?php echo $row['LastActive']; ?></td>

                                <td class="action">
                                    <i class="fa-solid fa-eye" style="cursor:pointer;"
                                        onclick="viewDevice('<?php echo $formatted_id; ?>', '<?php echo $row['VersionCode']; ?>', '<?php echo $row['MACAddress']; ?>', '<?php echo $row['Connectivity']; ?>', '<?php echo $row['Status']; ?>', '<?php echo $row['LastActive']; ?>')"></i>

                                    <i class="fa-solid fa-rotate" style="cursor:pointer;"
                                        onclick="openUpdateModal('<?php echo $formatted_id; ?>', '<?php echo $row['MacAddress']; ?>')"></i>
                                </td>

                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center; padding:20px; color:#ff9999;'>Version not found</td></tr>";
                    }
                    ?>


                    <div id="viewModal" class="modal">
                        <div class="modal-content">
                            <span class="close-btn" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></span>
                            <h1>View Language</h1>
                            <div class="modal-grid">
                                <div>
                                    <h2>Version ID</h2>
                                    <p id="viewID">D00000</p>
                                </div>
                                <div>
                                    <h2>Version Code</h2>
                                    <p id="viewCode">-</p>
                                </div>
                                <div>
                                    <h2>MAC Address</h2>
                                    <p id="viewAddress">-</p>
                                </div>
                                <div>
                                    <h2>Connectivity</h2>
                                    <p id="viewConnectivity">-</p>
                                </div>

                                <div>
                                    <h2>Status</h2>
                                    <p id="viewStatus">-</p>
                                </div>

                                <div>
                                    <h2>Last Active</h2>
                                    <p id="viewActive">-</p>
                                </div>

                            </div>

                            <button class="back-btn" onclick="closeModal()">Back</button>
                        </div>
                    </div>




                </tbody>
            </table>

        </div>
    </div>

    <div id="updateModal" class="modal-overlay">
        <div class="modal-card">
            <button class="modal-close-btn" onclick="closeUpdateModal()">×</button>
            <h2 class="modal-title">Update Device</h2>
            <p style="color: rgba(255,255,255,0.7); margin-bottom: 20px;">
                <span id="modalDeviceId"></span> | <span id="modalMacAddress"></span>
            </p>
            <p class="modal-message">Are you sure you want to update this device with the latest version?</p>
            <div class="modal-buttons">
                <button class="modal-btn btn-cancel" onclick="closeUpdateModal()">Cancel</button>
                <button class="modal-btn btn-yes" onclick="confirmUpdate()">Yes</button>
            </div>
        </div>
    </div>

    <script>

        function openUpdateModal(deviceId, macAddress) {
        document.getElementById('modalDeviceId').innerText = deviceId;
        document.getElementById('modalMacAddress').innerText = macAddress;
        document.getElementById('updateModal').classList.add('show');
    }

    function closeUpdateModal() {
        document.getElementById('updateModal').classList.remove('show');
    }

    function confirmUpdate() {
        alert("Updating " + document.getElementById('modalDeviceId').innerText + "...");
        closeUpdateModal();
    }

        const version = document.getElementById("version");
        const status = document.getElementById("status");
        const connectivity = document.getElementById("connectivity");

        function applyFilters() {

            const params = new URLSearchParams();

            if (version.value != "")
                params.append("version", version.value);

            if (status.value != "")
                params.append("status", status.value);

            if (connectivity.value != "")
                params.append("connectivity", connectivity.value);

            if (searchBox.value != "")
                params.append("search", searchBox.value);

            window.location.href = "deviceManagement.php?" + params.toString();

        }
        version.addEventListener("change", applyFilters);
        status.addEventListener("change", applyFilters);
        connectivity.addEventListener("change", applyFilters);

        const searchBox = document.getElementById("searchBox");
        const searchBtn = document.getElementById("searchBtn");
        const recentList = document.getElementById("recentList");

        function saveRecent(text) {

            if (text == "") return;

            let arr = JSON.parse(localStorage.getItem("recentSearch")) || [];

            arr = arr.filter(x => x != text);

            arr.unshift(text);

            if (arr.length > 5)
                arr = arr.slice(0, 5);

            localStorage.setItem("recentSearch", JSON.stringify(arr));

        }

        function showRecent() {

            let arr = JSON.parse(localStorage.getItem("recentSearch")) || [];

            recentList.innerHTML = "";

            if (arr.length == 0) {

                recentList.style.display = "none";
                return;

            }

            arr.forEach(item => {

                let div = document.createElement("div");

                div.className = "recent-item";

                div.innerHTML = '<i class="fa fa-clock"></i> ' + item;

                div.onclick = function () {

                    searchBox.value = item;
                    goSearch();

                };

                recentList.appendChild(div);

            });

            recentList.style.display = "block";

        }

        function goSearch() {

            saveRecent(searchBox.value);

            const params = new URLSearchParams();

            if (version.value != "")
                params.append("version", version.value);

            if (status.value != "")
                params.append("status", status.value);

            if (connectivity.value != "")
                params.append("connectivity", connectivity.value);

            if (searchBox.value != "")
                params.append("search", searchBox.value);

            window.location.href = "deviceManagement.php?" + params.toString();

        }

        searchBtn.onclick = goSearch;

        searchBox.addEventListener("keypress", function (e) {

            if (e.key === "Enter") {

                goSearch();

            }

        });

        searchBox.addEventListener("focus", showRecent);

        searchBox.addEventListener("input", showRecent);

        document.addEventListener("click", function (e) {

            if (!e.target.closest(".search-wrapper")) {

                recentList.style.display = "none";

            }

        });



        function viewDevice(id, code, mac, connectivity, status, active) {
            document.getElementById("viewID").innerText = id;
            document.getElementById("viewCode").innerText = code;
            document.getElementById("viewAddress").innerText = mac; // Kung wala sa DB, magiging undefined o empty
            document.getElementById("viewConnectivity").innerText = connectivity;
            document.getElementById("viewStatus").innerText = status;
            document.getElementById("viewActive").innerText = active;
            document.getElementById("viewModal").style.display = "flex";
        }
        function closeModal() { document.getElementById("viewModal").style.display = "none"; }



        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                document.querySelectorAll('.row-checkbox').forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        }

        const selectAllFiles = document.getElementById('selectAllFiles');
        if (selectAllFiles) {
            selectAllFiles.addEventListener('change', function () {
                document.querySelectorAll('.file-checkbox').forEach(checkbox => {
                    checkbox.checked = selectAllFiles.checked;
                });
            });
        }



    </script>

</body>

</html>