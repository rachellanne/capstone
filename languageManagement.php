<?php
include("db.php");


if (isset($_POST['add_language'])) {
    $LanguageName = mysqli_real_escape_string($conn, $_POST['LanguageName']);
    $Status = "Active";
    $Translation = 0;
    $FileName = "";

    if (isset($_FILES['FileName']['name']) && $_FILES['FileName']['name'] != "") {
        $FileName = mysqli_real_escape_string($conn, $_FILES['FileName']['name']);
        $tmp = $_FILES['FileName']['tmp_name'];
        if (!is_dir('uploads')) { mkdir('uploads', 0777, true); } // Siguraduhing may folder
        move_uploaded_file($tmp, "uploads/" . $FileName);
    }

    $query = "INSERT INTO languageManagement (LanguageName, Translation, FileName, Status) 
              VALUES ('$LanguageName', '$Translation', '$FileName', '$Status')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Language Added Successfully'); window.location='languageManagement.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}


if (isset($_POST['update_language'])) {
    $id = mysqli_real_escape_string($conn, $_POST['EditID']);
    $LanguageName = mysqli_real_escape_string($conn, $_POST['EditLanguageName']);
    $Status = mysqli_real_escape_string($conn, $_POST['EditStatus']);

    if (isset($_FILES['EditFileName']['name']) && $_FILES['EditFileName']['name'] != "") {
        $FileName = mysqli_real_escape_string($conn, $_FILES['EditFileName']['name']);
        move_uploaded_file($_FILES['EditFileName']['tmp_name'], "uploads/" . $FileName);
        
        $sql = "UPDATE languageManagement SET LanguageName='$LanguageName', FileName='$FileName', Status='$Status' WHERE ID='$id'";
    } else {
        $sql = "UPDATE languageManagement SET LanguageName='$LanguageName', Status='$Status' WHERE ID='$id'";
    }

    if(mysqli_query($conn, $sql)){
        echo "<script>alert('Language Updated Successfully'); window.location='languageManagement.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

$total_languages_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM languageManagement WHERE FileName != ''");
$total_languages = mysqli_fetch_assoc($total_languages_query)['total'];

$active_languages_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM languageManagement WHERE Status = 'Active' AND FileName != ''");
$total_active_languages = mysqli_fetch_assoc($active_languages_query)['total'];

$total_files_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM fileManagement");
$total_merged_files = mysqli_fetch_assoc($total_files_query)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Language Management</title>

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

        .line {
            position: absolute;
            height: 2px;
            background: white;
            bottom: 0;
            left: 0;
            width: 0;
            transition: all 0.3s ease;
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
                    <li class="active"><i class="fa-solid fa-globe"></i> Language</li>
                </a>
                <a href="deviceManagement.php">
                    <li><i class="fa-solid fa-desktop"></i> Device</li>
                </a>
                <li><i class="fa-solid fa-rotate"></i> Update</li>
            </ul>

            <a href="javascript:void(0);" id="sidebarLogoutLink">
                <ul class="menu logout">
                    <li><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</li>
                </ul>
            </a>
        </div>

        <div class="main">
            <h1 class="title">Language Management</h1>

            <div class="dashboard-grid">
                <div class="card">
                    <img src="Logo.png" alt="Icon">
                    <div>
                        <h3 id="firstCardTitle">Total Language</h3>
                      <h1><?php echo $total_languages; ?></h1>  </div>
                </div>

                <div class="card">
                <img src="Logo.png" alt="Icon">
                <div>
                    <h3>Active Language</h3>
                    <h1><?php echo $total_active_languages; ?></h1>
                </div>
            </div>

                <div class="card">
                    <img src="Logo.png" alt="Icon">
                    <div>
                        <h3>Total Translation</h3>
                        <h1>167,571</h1>
                    </div>
                </div>
            </div>

            <div class="tabs-container">
                <div class="tabs">
                    <p class="tab-item active-tab" onclick="switchTab('language', this)">Language</p>
                    <p class="tab-item" onclick="switchTab('file', this)">File</p>
                </div>
                <div class="line" id="tabLine"></div>
            </div>

            <div class="topBar">
                <div id="filterWrapper">
                    <select id="langSorter">
                        <option value="ASC">Language ID &gt; Ascending</option>
                        <option value="DESC">Language ID &gt; Descending</option>
                    </select>
                    <select id="fileSorter" style="display: none;">
                        <option value="DESC">File ID &gt; Descending</option>
                        <option value="ASC">File ID &gt; Ascending</option>
                    </select>
                </div>

                <div class="buttons" id="controlButtons">
                    <button id="mergeBtn">Merge File</button>
                    <button id="addLanguageBtn">Add Language</button>
                    <button id="archiveBtn" class="archive-btn" style="display: none;"><i
                            class="fa-solid fa-box-archive"></i> Archives</button>
                </div>
            </div>

            <div id="languageSection" class="ui-section active-section">
                <table id="languageTable">
                    <thead>
                        <tr>
                            <td><input type="checkbox" id="selectAll" class="checkbox"></td>
                            <th>Language ID</th>
                            <th>Language Name</th>
                            <th>Translation</th>
                            <th>File Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM languageManagement ORDER BY ID ASC");

                        while ($row = mysqli_fetch_assoc($result)) {
                            $db_id = $row['ID'];
                            $db_formatted_id = "L" . str_pad($db_id, 5, "0", STR_PAD_LEFT);
                            $db_name = $row['LanguageName'];
                            $db_translation = $row['Translation'];
                            $db_file = $row['FileName'];
                            $db_status = $row['Status'];
                            ?>
                            <tr data-id="<?php echo $db_id; ?>">
                                <td><input type="checkbox" class="checkbox row-checkbox" value="<?php echo $db_id; ?>"></td>
                                <td><?php echo $db_formatted_id; ?></td>
                                <td><?php echo $db_name; ?></td>
                                <td><?php echo $db_translation; ?></td>
                                <td><span class="file-badge"><i class="fa-regular fa-file-lines"></i>
                                        <?php echo $db_file ? $db_file : 'Walang File'; ?></span></td>
                                <td>
                                    <?php if ($db_status == "Active") { ?>
                                        <span class="status-active">● Active</span>
                                    <?php } else { ?>
                                        <span class="status-inactive">● Inactive</span>
                                    <?php } ?>
                                </td>
                                <td class="action">
                                    <i class="fa-solid fa-eye"
                                        onclick="openModal('<?php echo $db_formatted_id; ?>', '<?php echo $db_name; ?>', '<?php echo $db_file; ?>', '<?php echo $db_translation; ?>', '<?php echo $db_status; ?>')"></i>
                                    <i class="fa-solid fa-pen-to-square"
                                        onclick="openEditModal('<?php echo $db_id; ?>', '<?php echo $db_formatted_id; ?>', '<?php echo $db_name; ?>', '<?php echo $db_file; ?>', '<?php echo $db_translation; ?>', '<?php echo $db_status; ?>')"></i>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="fileSection" class="ui-section">
                <table id="fileTable">
                    <thead>
                        <tr>
                            <td><input type="checkbox" id="selectAllFiles" class="checkbox"></td>
                            <th>File ID</th>
                            <th>File Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $file_result = mysqli_query($conn, "SELECT * FROM fileManagement ORDER BY ID DESC");
                        while ($file_row = mysqli_fetch_assoc($file_result)) {
                            $f_id = $file_row['ID'];
                            $f_formatted_id = "F" . str_pad($f_id, 5, "0", STR_PAD_LEFT);
                            $f_name = !empty($file_row['FileName']) ? $file_row['FileName'] : 'No Name File';
                            $formatted_date = date("F d, Y", strtotime($file_row['Date']));
                            $formatted_time = date("h:i A", strtotime($file_row['Time']));
                            ?>
                            <tr data-id="<?php echo $f_id; ?>">
                                <td><input type="checkbox" class="checkbox file-checkbox" value="<?php echo $f_id; ?>"></td>
                                <td><?php echo $f_formatted_id; ?></td>
                                <td><span class="file-badge"><i class="fa-regular fa-file-lines"></i>
                                        <?php echo $f_name; ?></span></td>
                                <td><?php echo $formatted_date; ?></td>
                                <td><?php echo $formatted_time; ?></td>
                                <td class="action">
                                    <i class="fa-solid fa-eye" title="View File"></i>
                                    <i class="fa-solid fa-box-archive" title="Archive File"></i>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="modal-overlay" id="mergeModal">
        <div class="modal-card">
            <button class="modal-close-btn" id="closeModalX"><i class="fa-solid fa-xmark"></i></button>
            <div class="modal-title">Merge Files</div>
            <div class="modal-message">Are you sure you want to merge these files?</div>
            <div class="modal-buttons">
                <button class="modal-btn btn-cancel" id="closeModalCancel">Cancel</button>
                <button class="modal-btn btn-yes" id="confirmMerge">Yes</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="addLanguageModal">
        <div class="modal-card">
           <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">     enctype="multipart/form-data">
                <button type="button" class="modal-close-btn" id="closeAddX"><i class="fa-solid fa-xmark"></i></button>
                <div class="modal-title">Add Language</div>
                <div class="modal-form-grid">
                    <div class="form-group">
                        <label>Language Name</label>
                        <input type="text" id="langNameInput" name="LanguageName" required>
                    </div>
                    <div class="form-group">
                        <label>File</label>
                        <input type="file" id="langFileInput" name="FileName">
                    </div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="modal-btn btn-cancel" id="closeAddBack">Back</button>
                    <button type="submit" class="modal-btn btn-yes" name="add_language" id="confirmAdd">Add</button>
                </div>
            </form>
        </div>
    </div>

    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></span>
            <h1>View Language</h1>
            <div class="modal-grid">
                <div>
                    <h2>Language ID</h2>
                    <p id="viewID">L00000</p>
                </div>
                <div>
                    <h2>Language Name</h2>
                    <p id="viewName">-</p>
                </div>
                <div>
                    <h2>File</h2>
                    <p id="viewFile">-</p>
                </div>
                <div>
                    <h2>Translation</h2>
                    <p id="viewTranslation">0</p>
                </div>
            </div>
            <div class="status-box">
                <h2>Status</h2>
                <p id="viewStatus">-</p>
            </div>
            <button class="back-btn" onclick="closeModal()">Back</button>
        </div>
    </div>
    

    <div id="editModal" class="modal">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST"
                enctype="multipart/form-data">
                <span class="close-btn" onclick="closeEditModal()"><i class="fa-solid fa-xmark"></i></span>
                <h1>Edit Language</h1>
                <input type="hidden" id="editID" name="EditID">
                <div class="modal-grid">
                    <div>
                        <h2>Language ID</h2>
                        <p id="editFormattedID" style="color: #ffce1b; font-weight: bold; font-size: 18px;">L00001</p>
                    </div>
                    <div>
                        <h2>Language Name</h2><input type="text" id="editName" name="EditLanguageName" required>
                    </div>
                    <div>
                        <h2>File</h2>
                        <input type="file" name="EditFileName">
                        <p id="currentFileLabel" style="font-size:13px; margin-top:5px; color:#aaa;">Kasalukuyan: -</p>
                    </div>
                    <div>
                        <h2>Translation</h2>
                        <p id="editTranslation">0</p>
                    </div>
                </div>
                <div class="status-box">
                    <h2>Status</h2>
                    <select id="editStatus" name="EditStatus"
                        style="background:#161f33; color:white; padding:10px; border-radius:8px; border:1px solid rgba(255,255,255,.2); width: 50%;">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:40px;">
                    <button type="button" class="back-btn" onclick="closeEditModal()"
                        style="margin:0; background:#666;">Cancel</button>
                    <button type="submit" name="update_language" class="back-btn"
                        style="margin:0; background:#0f5f2d;">Save Changes</button>
                </div>
            </form>
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
        const PHP_TOTAL_LANGUAGES = "<?php echo $total_languages; ?>";
        const PHP_TOTAL_MERGED_FILES = "<?php echo $total_merged_files; ?>";

        function updateTabLine(element) {
            const tabLine = document.getElementById('tabLine');
            const containerRect = document.querySelector('.tabs-container').getBoundingClientRect();
            const tabRect = element.getBoundingClientRect();
            const leftOffset = tabRect.left - containerRect.left;

            tabLine.style.width = `${tabRect.width}px`;
            tabLine.style.transform = `translateX(${leftOffset}px)`;
        }

        function switchTab(tabName, element) {
            document.querySelectorAll('.tab-item').forEach(item => item.classList.remove('active-tab'));
            element.classList.add('active-tab');

            updateTabLine(element);

            const langSection = document.getElementById('languageSection');
            const fileSection = document.getElementById('fileSection');
            const langSorter = document.getElementById('langSorter');
            const fileSorter = document.getElementById('fileSorter');

            const mergeBtn = document.getElementById('mergeBtn');
            const addLanguageBtn = document.getElementById('addLanguageBtn');
            const archiveBtn = document.getElementById('archiveBtn');

            const firstCardTitle = document.getElementById('firstCardTitle');
            const firstCardValue = document.getElementById('firstCardValue');

            if (tabName === 'language') {
                langSection.style.display = 'block';
                fileSection.style.display = 'none';

                langSorter.style.display = 'inline-block';
                fileSorter.style.display = 'none';

                if (mergeBtn) mergeBtn.style.display = 'inline-block';
                if (addLanguageBtn) addLanguageBtn.style.display = 'inline-block';
                if (archiveBtn) archiveBtn.style.display = 'none';

                if (firstCardTitle) firstCardTitle.innerText = "Total Language";
                if (firstCardValue) firstCardValue.innerText = PHP_TOTAL_LANGUAGES;
            } else {
                fileSection.style.display = 'block';
                langSection.style.display = 'none';

                fileSorter.style.display = 'inline-block';
                langSorter.style.display = 'none';

                if (mergeBtn) mergeBtn.style.display = 'none';
                if (addLanguageBtn) addLanguageBtn.style.display = 'none';
                if (archiveBtn) archiveBtn.style.display = 'inline-block';

                if (firstCardTitle) firstCardTitle.innerText = "Total Merged File";
                if (firstCardValue) firstCardValue.innerText = PHP_TOTAL_MERGED_FILES;
            }
        }

        function sortTableRows(tableId, isAscending) {
            const table = document.getElementById(tableId);
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort((rowA, rowB) => {
                const idA = parseInt(rowA.getAttribute('data-id'), 10);
                const idB = parseInt(rowB.getAttribute('data-id'), 10);
                return isAscending ? idA - idB : idB - idA;
            });

            rows.forEach(row => tbody.appendChild(row));
        }

        document.getElementById('langSorter').addEventListener('change', function () {
            const order = this.value;
            sortTableRows('languageTable', order === 'ASC');
        });

        document.getElementById('fileSorter').addEventListener('change', function () {
            const order = this.value;
            sortTableRows('fileTable', order === 'ASC');
        });

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

        const mergeBtn = document.getElementById('mergeBtn');
        const mergeModal = document.getElementById('mergeModal');
        const closeModalX = document.getElementById('closeModalX');
        const closeModalCancel = document.getElementById('closeModalCancel');
        const confirmMerge = document.getElementById('confirmMerge');

        if (mergeBtn) mergeBtn.addEventListener('click', () => { mergeModal.classList.add('show'); });
        closeModalX.addEventListener('click', () => { mergeModal.classList.remove('show'); });
        closeModalCancel.addEventListener('click', () => { mergeModal.classList.remove('show'); });
        confirmMerge.addEventListener('click', () => {
            alert('Files successfully merged!');
            mergeModal.classList.remove('show');
        });

        const addLanguageBtn = document.getElementById('addLanguageBtn');
        const addLanguageModal = document.getElementById('addLanguageModal');
        const closeAddX = document.getElementById('closeAddX');
        const closeAddBack = document.getElementById('closeAddBack');

        if (addLanguageBtn) addLanguageBtn.addEventListener('click', () => { addLanguageModal.classList.add('show'); });
        closeAddX.addEventListener('click', () => { addLanguageModal.classList.remove('show'); });
        closeAddBack.addEventListener('click', () => { addLanguageModal.classList.remove('show'); });

        const sidebarLogoutLink = document.getElementById('sidebarLogoutLink');
        const logoutModal = document.getElementById('logoutModal');
        const closeLogoutCancel = document.getElementById('closeLogoutCancel');
        const confirmLogout = document.getElementById('confirmLogout');

        if (sidebarLogoutLink) {
            sidebarLogoutLink.addEventListener('click', () => {
                logoutModal.classList.add('show');
            });
        }
        if (closeLogoutCancel) {
            closeLogoutCancel.addEventListener('click', () => {
                logoutModal.classList.remove('show');
            });
        }
        if (confirmLogout) {
            confirmLogout.addEventListener('click', () => {
                window.location.href = 'index.php';
            });
        }

        window.addEventListener('click', (e) => {
            if (e.target === mergeModal) { mergeModal.classList.remove('show'); }
            if (e.target === addLanguageModal) { addLanguageModal.classList.remove('show'); }
            if (e.target === logoutModal) { logoutModal.classList.remove('show'); }
        });

        function openModal(id, name, file, translation, status) {
            document.getElementById("viewID").innerText = id;
            document.getElementById("viewName").innerText = name;
            document.getElementById("viewFile").innerText = file ? file : 'No File';
            document.getElementById("viewTranslation").innerText = translation;
            document.getElementById("viewStatus").innerText = status;
            document.getElementById("viewModal").style.display = "flex";
        }
        function closeModal() { document.getElementById("viewModal").style.display = "none"; }

        function openEditModal(rawId, formattedId, name, file, translation, status) {
            document.getElementById("editID").value = rawId;
            document.getElementById("editFormattedID").innerText = formattedId;
            document.getElementById("editName").value = name;
            document.getElementById("currentFileLabel").innerText = "Currently File : " + (file ? file : 'No File');
            document.getElementById("editTranslation").innerText = translation;
            document.getElementById("editStatus").value = status;
            document.getElementById("editModal").style.display = "flex";
        }
        function closeEditModal() { document.getElementById("editModal").style.display = "none"; }

        document.addEventListener("DOMContentLoaded", function () {
            const activeTab = document.querySelector('.tab-item.active-tab');
            if (activeTab) {
                updateTabLine(activeTab);
            }
        });
    </script>

</body>

</html>