<?php  
require_once 'core/models.php'; 
require_once 'core/handleForms.php'; 

if (!isset($_SESSION['username'])) {
	header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Activity Logs</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>

        body {
            background-color: #1f2226;
            color: #ffffff;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            
        }

   
        .navbar {
            background: linear-gradient(90deg, #5e17eb, #311b92);
        }
        .navbar-brand, .nav-link {
            color: #ffffff !important;
        }
        .navbar-brand:hover, .nav-link:hover {
            color: #c084fc !important;
        }

        .table {
            background-color: #1f2937;
            color: #eaeaea;
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background-color: #4c1d95;
            color: #ffffff;
        }
        .table tbody tr:nth-child(even) {
            background-color: #2c2f33;
        }
        .table tbody tr:hover {
            background-color: #4c1d95;
            color: #ffffff;
        }

        .btn-custom {
            background-color: #5e17eb;
            color: white;
            border: none;
            transition: 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #311b92;
            color: #ffffff;
        }
        .btn-danger {
            background-color: #ff5c5c;
        }
        .btn-danger:hover {
            background-color: #ff3b3b;
        }

        .btn-second {
            border: 1px solid #5e17eb;
            background-color: none;
            color: #fff;
            font-weight: 600;
        }

        .btn-second:hover {
            border: 1px solid #5e17eb;
            background-color: white;
            color: #5e17eb;
            font-weight: 600;
        } 
        .form-control {
            background-color: #2c2f33;
            color: #eaeaea;
            border: 1px solid #5e17eb;
        }
        .form-control:focus {
            background-color: #2c2f33;
            border-color: #c084fc;
            box-shadow: none;
            color: #ffffff;
        }

        .message {
            background-color: #311b92;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
        }

        .container {
            padding-top: 5%;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Cybersecurity Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../2_login_with_response/core/handleForms.php?logoutUserBtn=1">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
	<div class="container">
		<table style="width: 100%;" cellpadding="20" class="table table-hover table-dark">
			<tr>
				<th>Activity Log ID</th>
				<th>Operation</th>
				<th>ID</th>
				<th>Full Name</th>
				<th>Specialization</th>
				<th>Username</th>
				<th>Date Added</th>
			</tr>
			<?php $getAllActivityLogs = getAllActivityLogs($pdo); ?>
			<?php foreach ($getAllActivityLogs as $row) { ?>
			<tr>
				<td><?php echo $row['activity_log_id']; ?></td>
				<td><?php echo $row['operation']; ?></td>
				<td><?php echo $row['id']; ?></td>
				<td><?php echo $row['full_name']; ?></td>
				<td><?php echo $row['specialization']; ?></td>
				<td><?php echo $row['username']; ?></td>
				<td><?php echo $row['date_added']; ?></td>
			</tr>
			<?php } ?>
		</table>
</body>
</html>