<?php
session_start();

// Load student, quiz, and announcement data
$studentsFile = '../json/students.json';
$quizzesFile = '../json/quizzes.json';
$announcementsFile = '../json/announcements.json';
$studentsData = json_decode(file_get_contents($studentsFile), true);
$quizzesData = json_decode(file_get_contents($quizzesFile), true);
$announcementsData = json_decode(file_get_contents($announcementsFile), true);

// Check if user is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header('Location: ../login.html');
    exit();
}

$username = $_SESSION['user']['username'];
$student = null;

// Find the logged-in student
foreach ($studentsData['students'] as $s) {
    if ($s['username'] === $username) {
        $student = $s;
        break;
    }
}

if (!$student) {
    echo "Student not found.";
    exit();
}

// Sort announcements by date (most recent first)
usort($announcementsData['announcements'], function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- BOILERPLATE -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- PAGE -->
        <title>Don Carlo Cavina School / Student Dashboard</title>
        <link rel="icon" type="image/png" href="../assets/img/logo.png">

        <!-- CSS -->
        <link rel="stylesheet" href="../css/reset.css">
        <link rel="stylesheet" href="../css/dashboard_student.css">
    </head>

    <body>
        <header>
            <div class="welcome_container">
                <div class="welcome_box">
                    <h2>Welcome, <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>!</h2>
                    <a class="logout_a" href="logout.php">> Logout</a>
                </div>
            </div>
            
            <div class="announcements_container">
                <h2 class="announcements_h2">Announcements</h2>
                <div>
                    <?php if (!empty($announcementsData['announcements'])): ?>
                        <?php foreach ($announcementsData['announcements'] as $announcement): ?>
                            <div>
                                <p><strong><?php echo htmlspecialchars($announcement['date']); ?></strong></p>
                                <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                                <p class="announcements_p"><?php echo htmlspecialchars($announcement['text']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No announcements available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        
        <div class="subjects_title_container">
            <div class="subjects_title_child_box">
                <h2>Subjects</h2>
            </div>
        </div>

        <div class="subjects_container">
            <?php foreach ($student['subjects'] as $subject => $categories): ?>
                <div class="subjects_per_container">
                    <h3><?php echo htmlspecialchars($subject); ?></h3>
                    <?php foreach ($categories as $category => $scores): ?>
                        <h4><?php echo htmlspecialchars($category); ?></h4>
                        <ul>
                            <?php foreach ($scores as $index => $score): ?>
                                <li><?php echo htmlspecialchars($index . ' = ' . $score); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="quizzes_title_container">
            <div class="quizzes_title_child_box">
                <h2>Quizzes</h2>
            </div>
        </div>

        <div class="quiz_container">
            <?php foreach ($student['subjects'] as $subject => $categories): ?>
                <?php if (isset($quizzesData['quizzes'][$subject])): ?>
                    <div class="quiz_per_container">
                        <h3><?php echo htmlspecialchars($subject); ?></h3>
                        <ul>
                            <?php foreach ($quizzesData['quizzes'][$subject]['Quiz'] as $index => $link): ?>
                                <li>
                                    <a class="quiz_a" href="<?php echo htmlspecialchars($link); ?>" target="_blank">Quiz <?php echo htmlspecialchars($index); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </body>
</html>