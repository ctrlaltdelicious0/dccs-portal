<?php
session_start();

// Load admin, student, announcement, and quiz data
$adminsFile = '../json/admins.json';
$studentsFile = '../json/students.json';
$announcementsFile = '../json/announcements.json';
$quizzesFile = '../json/quizzes.json';

$adminsData = json_decode(file_get_contents($adminsFile), true);
$studentsData = json_decode(file_get_contents($studentsFile), true);
$announcementsData = json_decode(file_get_contents($announcementsFile), true);
$quizzesData = json_decode(file_get_contents($quizzesFile), true);

// Check if user is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.html');
    exit();
}

$username = $_SESSION['user']['username'];
$admin = null;

// Find the logged-in admin
foreach ($adminsData['admins'] as $a) {
    if ($a['username'] === $username) {
        $admin = $a;
        break;
    }
}

if (!$admin) {
    echo "Admin not found.";
    exit();
}

// Sort students by last name
usort($studentsData['students'], function($a, $b) {
    return strcmp($a['last_name'], $b['last_name']);
});

// Save sorted students back to JSON
file_put_contents($studentsFile, json_encode($studentsData, JSON_PRETTY_PRINT));

// Sort announcements by date (most recent first)
usort($announcementsData['announcements'], function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Handle form submissions for adding, editing, and removing announcements, scores, and quizzes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_score'])) {
        $studentIndex = $_POST['student_index'];
        $subject = $_POST['subject'];
        $category = $_POST['category'];
        $scoreIndex = $_POST['score_index'];
        $newScore = $_POST['new_score'];

        $studentsData['students'][$studentIndex]['subjects'][$subject][$category][$scoreIndex] = $newScore;
        file_put_contents($studentsFile, json_encode($studentsData, JSON_PRETTY_PRINT));
    } elseif (isset($_POST['delete_score'])) {
        $studentIndex = $_POST['student_index'];
        $subject = $_POST['subject'];
        $category = $_POST['category'];
        $scoreIndex = $_POST['score_index'];

        unset($studentsData['students'][$studentIndex]['subjects'][$subject][$category][$scoreIndex]);
        file_put_contents($studentsFile, json_encode($studentsData, JSON_PRETTY_PRINT));
    } elseif (isset($_POST['add_score'])) {
        $studentIndex = $_POST['student_index'];
        $subject = $_POST['subject'];
        $category = $_POST['category'];
        $newScore = $_POST['new_score'];

        $newScoreIndex = count($studentsData['students'][$studentIndex]['subjects'][$subject][$category]) + 1;
        $studentsData['students'][$studentIndex]['subjects'][$subject][$category][$newScoreIndex] = $newScore;
        file_put_contents($studentsFile, json_encode($studentsData, JSON_PRETTY_PRINT));
    } elseif (isset($_POST['add_announcement'])) {
        $newAnnouncement = [
            'date' => $_POST['date'],
            'title' => $_POST['title'],
            'text' => $_POST['text']
        ];
        $announcementsData['announcements'][] = $newAnnouncement;
        file_put_contents($announcementsFile, json_encode($announcementsData, JSON_PRETTY_PRINT));
    } elseif (isset($_POST['edit_announcement'])) {
        $index = $_POST['index'];
        $announcementsData['announcements'][$index] = [
            'date' => $_POST['date'],
            'title' => $_POST['title'],
            'text' => $_POST['text']
        ];
        file_put_contents($announcementsFile, json_encode($announcementsData, JSON_PRETTY_PRINT));
    } elseif (isset($_POST['delete_announcement'])) {
        $index = $_POST['index'];
        array_splice($announcementsData['announcements'], $index, 1);
        file_put_contents($announcementsFile, json_encode($announcementsData, JSON_PRETTY_PRINT));
    } 

    // ============================
    // QUIZ MANAGEMENT FUNCTIONALITY
    // ============================
    elseif (isset($_POST['add_quiz'])) {
        // Add a new quiz link
        $subject = $_POST['subject'];
        $quizNumber = $_POST['quiz_number'];
        $quizLink = $_POST['quiz_link'];

        $quizzesData['quizzes'][$subject]['Quiz'][$quizNumber] = $quizLink;
        file_put_contents($quizzesFile, json_encode($quizzesData, JSON_PRETTY_PRINT));
    } elseif (isset($_POST['edit_quiz'])) {
        // Edit an existing quiz link
        $subject = $_POST['subject'];
        $quizNumber = $_POST['quiz_number'];
        $newQuizLink = $_POST['new_quiz_link'];

        if (isset($quizzesData['quizzes'][$subject]['Quiz'][$quizNumber])) {
            $quizzesData['quizzes'][$subject]['Quiz'][$quizNumber] = $newQuizLink;
            file_put_contents($quizzesFile, json_encode($quizzesData, JSON_PRETTY_PRINT));
        }
    } elseif (isset($_POST['delete_quiz'])) {
        // Delete a quiz link
        $subject = $_POST['subject'];
        $quizNumber = $_POST['quiz_number'];

        if (isset($quizzesData['quizzes'][$subject]['Quiz'][$quizNumber])) {
            unset($quizzesData['quizzes'][$subject]['Quiz'][$quizNumber]);
            file_put_contents($quizzesFile, json_encode($quizzesData, JSON_PRETTY_PRINT));
        }
    } 
    elseif (isset($_POST['save_all_changes'])) {
    if (isset($_POST['all_scores_data'])) {
        $updatedScores = json_decode($_POST['all_scores_data'], true);

        foreach ($updatedScores as $score) {
            $studentsData['students'][$score['studentIndex']]['subjects'][$score['subject']][$score['category']][$score['scoreIndex']] = $score['newScore'];
        }

        file_put_contents($studentsFile, json_encode($studentsData, JSON_PRETTY_PRINT));
    }

    header('Location: dashboard_admin.php');
    exit();
}

    // Redirect to refresh the page after processing
    header('Location: dashboard_admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- BOILERPLATE -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- PAGE -->
        <title>Don Carlo Cavina School / Admin Dashboard</title>
        <link rel="icon" type="image/png" href="../assets/img/logo.png">

        <!-- CSS -->
        <link rel="stylesheet" href="../css/reset.css">
        <link rel="stylesheet" href="../css/dashboard_admin.css">
    </head>

    <body>
        <div class="welcome_container">
            <div class="welcome_child">
                <h2>Welcome, <?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?>!</h2>
                <a class="logout_a" href="logout.php">> Logout</a>
            </div>
        </div>
        
        <div class="announcements_container">
            <div class="announcements_child">
                <h2 class="announcements_h2">Manage Announcements</h2>

                <form method="POST" class="announcements_form">
                    <input class="announcements_input" type="text" name="date" placeholder="MM/DD/YY" pattern="\d{2}/\d{2}/\d{2}" required>
                    <input class="announcements_input" type="text" name="title" placeholder="Title" required>
                    <textarea class="announcements_input" name="text" placeholder="Announcement Text" required></textarea>
                    <button class="announcements_button" type="submit" name="add_announcement">Add Announcement</button>
                </form>

                <div class="existing_announcements_container">
                    <?php foreach ($announcementsData['announcements'] as $index => $announcement): ?>
                        <div class="existing_announcements_per_container">
                            <p><strong><?php echo htmlspecialchars($announcement['date']); ?></strong></p>
                            <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                            <p><?php echo htmlspecialchars($announcement['text']); ?></p>

                            <!-- Edit Announcement Form -->
                            <form method="POST" class="announcements_form">
                                <input class="announcements_input" type="hidden" name="index" value="<?php echo $index; ?>">
                                <input class="announcements_input" type="text" name="date" value="<?php echo htmlspecialchars($announcement['date']); ?>" required>
                                <input class="announcements_input" type="text" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
                                <textarea class="announcements_input" name="text" required><?php echo htmlspecialchars($announcement['text']); ?></textarea>
                                <button class="announcements_button" type="submit" name="edit_announcement">Save Changes</button>
                                <button class="announcements_button" type="submit" name="delete_announcement" onclick="return confirm('Are you sure you want to delete this announcement?');">Remove</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="quiz_container">
            <div class="quiz_child">
                <h2 class="quiz_h2">Manage Quiz Links</h2>

                <!-- Add a New Quiz Link -->
                <form method="POST" class="quiz_new_form">
                    <select class="quiz_input" name="subject" required>
                        <option value="" disabled selected>Select Subject</option>
                        <?php foreach ($quizzesData['quizzes'] as $subject => $data): ?>
                            <option value="<?php echo htmlspecialchars($subject); ?>"><?php echo htmlspecialchars($subject); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input class="quiz_input" type="text" name="quiz_number" placeholder="Quiz Number (e.g., 1, 2, 3)" required>
                    <input class="quiz_input" type="url" name="quiz_link" placeholder="Quiz Link (Google Forms URL)" required>
                    <button class="quiz_button" type="submit" name="add_quiz">Add Quiz</button>
                </form>

                <!-- Display Existing Quizzes -->
                <div class="quiz_existing_container">
                    <?php foreach ($quizzesData['quizzes'] as $subject => $quizCategory): ?>
                        <div class="quiz_existing_child">
                            <h3><?php echo htmlspecialchars($subject); ?></h3>

                            <ul>
                                <?php foreach ($quizCategory['Quiz'] as $quizNumber => $quizLink): ?>
                                    <li class="quiz_existing_li">
                                        <strong>Quiz <?php echo htmlspecialchars($quizNumber); ?>:</strong>
                                        <a class="quiz_a" href="<?php echo htmlspecialchars($quizLink); ?>" target="_blank"><?php echo htmlspecialchars($quizLink); ?></a>

                                        <!-- Edit and Remove Quiz Links -->
                                        <form class="quiz_existing_form" method="POST">
                                            <input  class="quiz_input" type="hidden" name="quiz_number" value="<?php echo htmlspecialchars($quizNumber); ?>">
                                            <input  class="quiz_input" type="hidden" name="subject" value="<?php echo htmlspecialchars($subject); ?>">
                                            <input  class="quiz_input" type="url" name="new_quiz_link" value="<?php echo htmlspecialchars($quizLink); ?>" required>
                                            <button class="quiz_button" type="submit" name="edit_quiz">Save</button>
                                            <button class="quiz_button" type="submit" name="delete_quiz" onclick="return confirm('Are you sure?');">Remove</button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="students_container">
            <div class="students_child">
                <h2 class="students_h2">Manage Student Scores</h2>
        
                <div class="students_all_container">
                    <?php foreach ($studentsData['students'] as $studentIndex => $student): ?>
                        <div class="students_per_container">
                            <h3><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></h3>
                            <?php foreach ($student['subjects'] as $subject => $categories): ?>
                                <h4><?php echo htmlspecialchars($subject); ?></h4>
                                <?php foreach ($categories as $category => $scores): ?>
                                    <h5><?php echo htmlspecialchars($category); ?></h5>
                                    <ul>
                                        <?php foreach ($scores as $scoreIndex => $score): ?>
                                            <li class="score_per">
                                                <span><?php echo htmlspecialchars($scoreIndex); ?> =</span>
                                                <form method="POST" class="score_input_container">
                                                    <input class="score_input" type="hidden" name="student_index" value="<?php echo $studentIndex; ?>">
                                                    <input class="score_input" type="hidden" name="subject" value="<?php echo htmlspecialchars($subject); ?>">
                                                    <input class="score_input" type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                                                    <input class="score_input" type="hidden" name="score_index" value="<?php echo $scoreIndex; ?>">
                                                    <input class="score_input" type="text" name="new_score" value="<?php echo htmlspecialchars($score); ?>" required>
                                                    <button class="score_button" type="submit" name="update_score">Save</button>
                                                    <button class="score_button" type="submit" name="delete_score" onclick="return confirm('Are you sure?');">Remove</button>
                                                </form>
                                            </li>
                                        <?php endforeach; ?>
                                        <li>
                                            <form method="POST" class="score_add_form">
                                                <input class="score_input" type="hidden" name="student_index" value="<?php echo $studentIndex; ?>">
                                                <input class="score_input" type="hidden" name="subject" value="<?php echo htmlspecialchars($subject); ?>">
                                                <input class="score_input" type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                                                <input class="score_input" type="text" name="new_score" placeholder="New Score" required>
                                                <button class="score_button" type="submit" name="add_score">Add</button>
                                            </form>
                                        </li>
                                    </ul>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form method="POST" class="save_all_form" id="saveAllForm">
                    <input type="hidden" id="allScoresData" name="all_scores_data">
                    <button class="save_all_button" type="submit" name="save_all_changes">Save All Changes</button>
                </form>

                <script>
                document.getElementById("saveAllForm").addEventListener("submit", function(event) {
                    let allScores = [];

                    document.querySelectorAll(".score_input_container").forEach(function(form) {
                        let studentIndex = form.querySelector("[name='student_index']").value;
                        let subject = form.querySelector("[name='subject']").value;
                        let category = form.querySelector("[name='category']").value;
                        let scoreIndex = form.querySelector("[name='score_index']").value;
                        let newScore = form.querySelector("[name='new_score']").value;

                        allScores.push({ studentIndex, subject, category, scoreIndex, newScore });
                    });

                    document.getElementById("allScoresData").value = JSON.stringify(allScores);
                });
                </script>
            </div>
        </div>
    </body>
</html>