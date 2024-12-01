<?php  

require_once 'dbConfig.php';
require_once 'models.php';


if (isset($_POST['insertUserBtn'])) {
	$Full_Name = trim($_POST['Full_Name']);
	$Email = trim($_POST['Email']);
	$Phone = trim($_POST['Phone']);
	$Specialization = trim($_POST['Specialization']);
	$Years_of_Experience = trim($_POST['Years_of_Experience']);
	$Certifications = trim($_POST['Certifications']);

	if (!empty($Full_Name) && !empty($Email) && !empty($Phone)) {
		$insertUser = insertNewUser($pdo, $Full_Name, $Email, $Phone,$Specialization,
		$Years_of_Experience, $Certifications, $_SESSION['username']);
		$_SESSION['status'] =  $insertUser['status']; 
		$_SESSION['message'] =  $insertUser['message']; 
		header("Location: ../index.php");
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../index.php");
	}

}


if (isset($_POST['editUserBtn'])) {
    // Validate and sanitize ID
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$id) {
        $_SESSION['message'] = "Invalid or missing user ID!";
        $_SESSION['status'] = '400';
        header("Location: ../index.php");
        exit;
    }

    // Retrieve and sanitize form inputs
    $Full_Name = trim($_POST['Full_Name'] ?? '');
    $Email = trim($_POST['Email'] ?? '');
    $Phone = trim($_POST['Phone'] ?? '');
    $Specialization = trim($_POST['Specialization'] ?? '');
    $Years_of_Experience = trim($_POST['Years_of_Experience'] ?? '');
    $Certifications = trim($_POST['Certifications'] ?? '');

    $Last_Updated = date('Y-m-d H:i:s');

    // Ensure all required fields are filled
    if (
        empty($Full_Name) ||
        empty($Email) ||
        empty($Phone) ||
        empty($Specialization) ||
        empty($Years_of_Experience) ||
        empty($Certifications)
    ) {
        $_SESSION['message'] = "Please ensure all fields are completed.";
        $_SESSION['status'] = '400';
        header("Location: ../editUser.php?id=$id");
        exit;
    }

    // Call the model function to update the user
    $updateUser = editUser(
        $pdo,
        $Full_Name,
        $Email,
        $Phone,
        $Specialization,
        $Years_of_Experience,
        $Certifications,
        $_SESSION['username'], // Assuming the logged-in user is the updater
        $Last_Updated,
        $id
    );

    // Handle the response from the model
    if ($updateUser['status'] === "200") {
        $_SESSION['message'] = $updateUser['message'];
        $_SESSION['status'] = '200';
        header("Location: ../index.php");
    } else {
        $_SESSION['message'] = $updateUser['message'];
        $_SESSION['status'] = '400';
        header("Location: ../editUser.php?id=$id");
    }
    exit;
}

if (isset($_POST['deleteUserBtn'])) {
    $id = $_POST['id']; 
    $deleteUser = deleteUser($pdo, $id);

    // Check the status key from the response array
    if ($deleteUser['status'] === "200") {
        $_SESSION['message'] = $deleteUser['message'];
        $_SESSION['status'] = '200'; // Success
    } else {
        $_SESSION['message'] = $deleteUser['message'];
        $_SESSION['status'] = '400'; // Failure
    }

    // Redirect after the deletion attempt
    header("Location: ../index.php");
    exit;
}


if (isset($_GET['searchBtn'])) {
	$searchForAUser = searchForAUser($pdo, $_GET['searchInput']);
	foreach ($searchForAUser as $row) {
		echo "<tr> 
				<td>{$row['Full_Name']}</td>
				<td>{$row['Email']}</td>
				<td>{$row['Phone']}</td>
				<td>{$row['Specialization']}</td>
				<td>{$row['Years_of_Experience']}</td>
				<td>{$row['Certifications']}</td>
			  </tr>";
	}
} 

?>