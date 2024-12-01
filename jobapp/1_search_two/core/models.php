<?php  

require_once 'dbConfig.php';

function getAllUsers($pdo) { 
    $sql = "SELECT id, Full_Name, Email, Phone, Specialization, Years_of_Experience, Certifications, Date_Added, Added_By, Last_Updated_By, Last_Updated FROM cybersecurity_specialists
            ORDER BY Full_Name ASC";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute();
    if ($executeQuery) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $users = $stmt->fetchAll();
        return $users;
    } else {
        print_r($stmt->errorInfo()); // Debugging aid
        return [];
    }
}

function getAllUsersBySearch($pdo, $search_query) {
	$sql = "SELECT id, Full_Name, Email, Phone, Specialization, Years_of_Experience, Certifications, Date_Added, Added_By, Last_Updated_By, Last_Updated  FROM cybersecurity_specialists WHERE 
			CONCAT(Full_Name,Email,
				Phone,
				Specialization,Years_of_Experience,Certifications, Date_Added, Added_by,
				Last_Updated_By,
				Last_Updated) 
			LIKE ?";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute(["%".$search_query."%"]);
	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}
function getUserByID($pdo, $id) {
	$sql = "SELECT * from cybersecurity_specialists WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function searchForAUser($pdo, $searchQuery) {
    $sql = "SELECT id, Full_Name, Email, Phone, Specialization, Years_of_Experience, Certifications
            FROM cybersecurity_specialists 
            WHERE CONCAT(Full_Name, Email, Phone, Specialization, Years_of_Experience, Certifications) LIKE ?";

    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute(["%" . $searchQuery . "%"]);

    if ($executeQuery) {
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    } else {
        
        print_r($stmt->errorInfo()); 
        return [];
    }
}

function insertAnActivityLog($pdo, $operation, $id, $full_name, $specialization, $username) {
    $sql = "INSERT INTO activity_logs (operation, id, full_name, specialization, username) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$operation, $id, $full_name, $specialization, $username]);

    if ($executeQuery) {
        return true;
    }
    return false;
}


function getAllActivityLogs($pdo) {
	$sql = "SELECT * FROM activity_logs 
			ORDER BY date_added DESC";
	$stmt = $pdo->prepare($sql);
	if ($stmt->execute()) {
		return $stmt->fetchAll();
	}
}
//insertABranch
function insertNewUser(
    $pdo, 
    $Full_Name, 
    $Email, 
    $Phone, 
    $Specialization, 
    $Years_of_Experience, 
    $Certifications, 
    $Added_By
) {
    $response = array();

    // Insert the new user into the database
    $sql = "INSERT INTO cybersecurity_specialists 
                (Full_Name,
                Email,
                Phone,
                Specialization,
                Years_of_Experience,
                Certifications,
                Added_By) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $insertUser = $stmt->execute([
        $Full_Name, 
        $Email, 
        $Phone, 
        $Specialization,
        $Years_of_Experience, 
        $Certifications, 
        $Added_By
    ]);

    if ($insertUser) {
        // Fetch the most recently added user for logging purposes
        $findInsertedItemSQL = "SELECT * FROM cybersecurity_specialists ORDER BY date_added DESC LIMIT 1";
        $stmtFindInsertedItem = $pdo->prepare($findInsertedItemSQL);
        $stmtFindInsertedItem->execute();
        $getUserID = $stmtFindInsertedItem->fetch();

        // Fetch the user_id of the logged-in user
        $loggedInUserId = getLoggedInUserId($pdo, $_SESSION['username']);
        if ($loggedInUserId) {
            // Insert an activity log for this operation
            $insertAnActivityLog = insertAnActivityLog(
                $pdo, 
                "INSERT", 
                $loggedInUserId, 
                $getUserID['Full_Name'], 
                $getUserID['Specialization'], 
                $_SESSION['username']
            );

            if ($insertAnActivityLog) {
                $response = array(
                    "status" => "200",
                    "message" => "User added successfully!"
                );
            } else {
                $response = array(
                    "status" => "400",
                    "message" => "Insertion of activity log failed!"
                );
            }
        } else {
            $response = array(
                "status" => "400",
                "message" => "Could not retrieve logged-in user ID."
            );
        }
    } else {
        $response = array(
            "status" => "400",
            "message" => "Insertion of user data failed!"
        );
    }

    return $response;
}


function editUser($pdo, $Full_Name, $Email, $Phone, $Specialization, $Years_of_Experience, $Certifications, $Last_Updated_By, $Last_Updated,  $id) {

    $response = array();
	$sql = "UPDATE cybersecurity_specialists
			SET Full_Name = ?,
				Email = ?,
				Phone = ?, 
				Specialization = ?, 
				Years_of_Experience = ?,
                Certifications = ?,
                Last_Updated_By = ?,
                Last_Updated = ?
			WHERE ID = ?
			";
    $stmt = $pdo->prepare($sql);
	$editUser = $stmt->execute([$Full_Name, $Email, $Phone, $Specialization, $Years_of_Experience, $Certifications, $Last_Updated_By, $Last_Updated, $id]);

    if ($editUser) {

		$findInsertedItemSQL = "SELECT * FROM cybersecurity_specialists WHERE ID = ?";
		$stmtfindInsertedItemSQL = $pdo->prepare($findInsertedItemSQL);
		$stmtfindInsertedItemSQL->execute([$id]);
		$getID = $stmtfindInsertedItemSQL->fetch(); 

		$insertAnActivityLog = insertAnActivityLog($pdo, "UPDATE", $getID['id'], 
        $getID['Full_Name'], $getID['Specialization'], 
        $_SESSION['username']);

		if ($insertAnActivityLog) {

			$response = array(
				"status" =>"200",
				"message"=>"Updated the employee successfully!"
			);
		}

		else {
			$response = array(
				"status" =>"400",
				"message"=>"Insertion of activity log failed!"
			);
		}

	}

	else {
		$response = array(
			"status" =>"400",
			"message"=>"An error has occured with the query!"
		);
	}

	return $response;
}



function deleteUser($pdo, $id) {
    $response = array();

    // Fetch the user being deleted for logging purposes
    $sql = "SELECT * FROM cybersecurity_specialists WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $getUserByID = $stmt->fetch();

    if ($getUserByID) {
        // Fetch the user_id of the logged-in user
        $loggedInUserId = getLoggedInUserId($pdo, $_SESSION['username']);
        if ($loggedInUserId) {
            // Log the delete action in the activity log
            $insertAnActivityLog = insertAnActivityLog(
                $pdo, 
                "DELETE", 
                $loggedInUserId, 
                $getUserByID['Full_Name'], 
                $getUserByID['Specialization'], 
                $_SESSION['username']
            );

            if ($insertAnActivityLog) {
                // Perform the delete operation
                $deleteSql = "DELETE FROM cybersecurity_specialists WHERE id = ?";
                $deleteStmt = $pdo->prepare($deleteSql);
                $deleteQuery = $deleteStmt->execute([$id]);

                if ($deleteQuery) {
                    $response = array(
                        "status" => "200",
                        "message" => "Deleted the user successfully!"
                    );
                    // Redirect to index.php after successful deletion
                    header("Location: ../index.php"); 
                    exit();
                } else {
                    $response = array(
                        "status" => "400",
                        "message" => "Failed to delete the user!"
                    );
                }
            } else {
                $response = array(
                    "status" => "400",
                    "message" => "Failed to log the delete action!"
                );
            }
        } else {
            $response = array(
                "status" => "400",
                "message" => "Could not retrieve logged-in user ID!"
            );
        }
    } else {
        $response = array(
            "status" => "400",
            "message" => "User not found!"
        );
    }

    return $response;
}

function getLoggedInUserId($pdo, $username) {
    $sql = "SELECT user_id FROM user_accounts WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['user_id'];
    }
    return null;
}
?>

