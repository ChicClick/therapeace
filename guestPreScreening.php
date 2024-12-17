<?php
include 'guest_screening_questions.php';
$groupedQuestions = [];
foreach ($questions as $question) {
    $groupedQuestions[$question['category']][] = $question;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheraPeace</title>
    <link rel="icon" type="image/svg+xml" href="images/TheraPeace Logo.svg">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="guestPreScreeningstyles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poly:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <!-- Navbar -->
        <nav>
            <div class="logo">
                <img src="images/TheraPeace Logo.svg" alt="TheraPeace Logo">
                <h1>TheraPeace</h1>
            </div>
            <div class="nav-container">
                <ul class="nav-links">
                    <li><a href="#">Home</a></li>
                    <li><a href="#about" data-nav-link>About Us</a></li>
                    <li><a href="#services" data-nav-link>Services</a></li>
                    <li><a href="#rates" data-nav-link>Rates</a></li>
                </ul>
                <button class="login-btn" onclick="window.location.href='loginlanding.html';">Login</button>
                <!-- Hamburger menu icon for smaller devices -->
                <div class="hamburger-menu" id="hamburger-menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </div>
        </nav>

        <!-- Pre-Screening Section -->
        <section class="pre-screening-section">
            <h2>Take our TheraQuick test!</h2>
            <div class="pre-screening-card">
                <div class="left-section">
                    <p>Pre-Screening</p>
                    <h3>QUICK CHECK </h3>
                    <p>Click the Log In button if already registered as a patient</p>
                </div>
                <div class="right-section">
                    <p>Complete this quick pre-screening form to officially register as a patient and help us prepare for your therapy journey.</p>
                    <br><ul>
                        <li><strong>Confirm Your Needs:</strong> Share essential details to ensure we align with the therapy recommended for you by your developmental pediatrician.</li>
                        <li><strong>Tailored Preparation:</strong> Your responses will help us customize your therapy experience and provide the best support.</li>
                        <li><strong>Confidential and Secure:</strong> All information is securely stored and used exclusively for your care.</li>
                    </ul>
                    <p><strong>Important Disclaimer:</strong>
                    Please only proceed with this form if your child has already received a diagnosis from a developmental pediatrician. This helps us provide the right therapy for your child.</p>
                    <button class="start-test-btn" onclick="showPreScreeningForm()">Start Test</button>
                </div>
            </div>
        </section>


        <!-- Pre-Screening Form (Initially Hidden) -->
        <div class="test-intro hidden">  <!-- 'hidden' class added here -->
            <h2>TheraQuick test!</h2>
            <div class="steps">
                <div class="step">
                    <span>1</span>
                </div>
                <span class="line"></span>
                <div class="step">
                    <span>2</span>
                </div>
                <span class="line"></span>
                <div class="step">
                    <span>3</span>
                </div>
                <span class="line"></span>
                <div class="step">
                    <span>4</span>
                </div>
                <span class="line hidden"></span>
                <div class="step">
                    <span>5</span>
                </div>
                <span class="line"></span>
                <div class="step">
                    <span>6</span>
                </div>
                <span class="line"></span>
                <div class="step">
                    <span>7</span>
                </div>
                <span class="line"></span>
                <div class="step">
                    <span>8</span>
                </div>
                <span class="line"></span>
                <div class="step">
                    <span>9</span>
                </div>
            </div>
        </div>

        <!-- Pre-Screening Form Section -->
        <section id="pre-screening-form" class="pre-screening-form hidden">
            <div class="form-container">
                <form action="submit_pre_screening.php" method="POST">

                    <?php $categoryIndex = 0;?>
                    <?php foreach ($groupedQuestions as $category => $categoryQuestions): ?>
                        <div class="category-section" id="category-<?=$categoryIndex?>" style="display: <?=($categoryIndex === 0) ? 'block' : 'none'?>">
                            <h3><?=htmlspecialchars($category)?></h3> <!-- Display category name -->

                            <?php foreach ($categoryQuestions as $question): ?>
                                <?php
// Check if the question should be skipped based on its properties
if (!empty($question['skip']) && $question['skip'] === true) {
    continue; // Skip this question
}
?>

                                <!-- Skip specific input types under General Input Group -->
                                <?php
// Define the questions to be skipped
$questionsToSkip = [];
if ($category === 'Personal Details') {
    $questionsToSkip = ['Date of Birth', 'Age', 'Sex'];
} elseif ($category === 'During Pregnancy') {
    $questionsToSkip = ['Age of mother during pregnancy', 'Age of father'];
} elseif ($category === 'Delivery') {
    $questionsToSkip = ['Hours of labor'];
} elseif ($category === 'Personal History') {
    $questionsToSkip = ['Ilan ang kapatid?', 'Pang ilan sa magkakapatid?'];
}

// Check if the current question should be skipped
if (in_array($question['questionText'], $questionsToSkip)) {
    continue;
}

$isRequired = $question['isRequired'] ? 'required' : '';
$requiredMark = $question['isRequired'] ? '<span class="required-asterisk">*</span>' : '';
$description = !empty($question['description']) ? $question['description'] : '';
?>

                                <div class="question-section">
                                        <label>
                                            <?=htmlspecialchars($question['questionText'])?> <?=$requiredMark?>
                                        </label>
                                        <?php if (!empty($description)): ?>
                                            <p class="question-description"><?=htmlspecialchars($description)?></p>
                                        <?php endif;?>

                                        <?php
$options = !empty($question['options']) ? explode(',', $question['options']) : [];
?>
                                        <?php if ($question['inputType'] === 'radio'): ?>
                                            <div class="option-group">
                                                <?php foreach ($options as $option): ?>
                                                    <div class="option-item">
                                                        <input type="radio" name="question_<?=$question['questionID']?>" value="<?=htmlspecialchars($option)?>">
                                                        <label><?=htmlspecialchars($option)?></label>
                                                    </div>
                                                <?php endforeach;?>
                                            </div>
                                        <?php elseif ($question['inputType'] === 'checkbox'): ?>
                                            <div class="option-group">
                                                <?php foreach ($options as $option): ?>
                                                    <div class="option-item">
                                                        <input type="checkbox" name="question_<?=$question['questionID']?>[]" value="<?=htmlspecialchars($option)?>">
                                                        <label><?=htmlspecialchars($option)?></label>
                                                    </div>
                                                <?php endforeach;?>
                                            </div>
                                        <?php elseif ($question['inputType'] === 'checkbox'): ?>
                                            <div class="option-group" id="<?=($question['questionID'] == 46) ? 'question-46' : ''?>">
                                                <?php foreach ($options as $option): ?>
                                                    <div class="option-item">
                                                        <input type="checkbox" name="question_<?=$question['questionID']?>[]" value="<?=htmlspecialchars($option)?>" <?=($question['questionID'] == 46) ? 'class="required-checkbox"' : ''?>>
                                                        <label><?=htmlspecialchars($option)?></label>
                                                    </div>
                                                <?php endforeach;?>
                                            </div>
                                        <?php elseif (in_array($question['inputType'], ['text', 'number', 'date'])): ?>
                                            <input type="<?=$question['inputType']?>" name="question_<?=$question['questionID']?>" class="styled-input" <?=$isRequired?>><br>
                                        <?php elseif ($question['inputType'] === 'select'): ?>
                                            <select name="question_<?=$question['questionID']?>" class="styled-select" <?=$isRequired?>>
                                                <?php foreach ($options as $option): ?>
                                                    <option value="<?=htmlspecialchars($option)?>"><?=htmlspecialchars($option)?></option>
                                                <?php endforeach;?>
                                            </select><br>
                                        <?php elseif ($question['inputType'] === 'textarea'): ?>
                                            <textarea name="question_<?=$question['questionID']?>" class="styled-textarea" <?=$isRequired?>></textarea><br>
                                        <?php endif;?>
                                    </div>
                                <?php endforeach;?>

                            <!-- General Input Group for similar types -->
                            <?php if ($category === 'Personal Details'): ?>
                                <div class="demographics-section" style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                    <div class="input-group">
                                        <label class="required" for="dob">Date of Birth</label>
                                        <input type="date" name="dob" id="dob" class="styled-input" required>
                                    </div>
                                    <div class="input-group">
                                        <label class="required" for="sex">Sex</label>
                                        <select name="sex" id="sex" class="styled-select" required>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endif;?>

                            <?php if ($category === 'During Pregnancy'): ?>
                                <div class="demographics-section" style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                    <div class="input-group">
                                        <label class="required" for="mother_age">Age of mother during pregnancy</label>
                                        <input type="number" name="mother_age" id="mother_age" class="styled-input" required>
                                    </div>
                                    <div class="input-group">
                                        <label for="father_age">Age of father:</label>
                                        <input type="number" name="father_age" id="father_age" class="styled-input">
                                    </div>
                                </div>
                            <?php endif;?>

                            <?php if ($category === 'Delivery'): ?>
                                <div class="demographics-section" style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                    <div class="input-group">
                                        <label for="labor_hours">Hours of labor</label>
                                        <input type="number" name="labor_hours" id="labor_hours" class="styled-input">
                                    </div>
                                </div>
                            <?php endif;?>

                            <?php if ($category === 'Personal History'): ?>
                                <div class="demographics-section" style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                    <div class="input-group">
                                        <label for="siblings">Ilan ang kapatid?</label>
                                        <input type="number" name="siblings" id="siblings" class="styled-input">
                                    </div>
                                    <div class="input-group">
                                        <label for="sibling_position">Pang-ilan sa magkakapatid?</label>
                                        <input type="number" name="sibling_position" id="sibling_position" class="styled-input">
                                    </div>
                                </div>
                            <?php endif;?>

                            <?php if ($categoryIndex === 0): ?>
                                <!-- Email and Phone Number fields styled like the question sections -->
                                <div class="question-section" style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                    <div class="input-group">
                                        <label class="required" for="email">Email</label>
                                        <input type="email" name="email" id="email" class="styled-input" required>
                                    </div>
                                    <div class="input-group">
                                        <label class="required" for="phone">Phone Number</label>
                                        <input type="tel" name="phone" id="phone" class="styled-input" required>
                                    </div>
                                </div>
                                <div class="question-section" style="display: flex;">
                                    <div class="input-group">
                                        <label class="required" for="guestName">Parent's Name</label>
                                        <input type="text" name="guestName" id="guestName" class="styled-input" required>
                                    </div>
                                </div>
                            <?php endif;?>

                            <!-- Navigation buttons -->
                                <div class="navigation-buttons">
                                    <?php if ($categoryIndex !== 0): ?>
                                        <button type="button" onclick="prevCategory(<?=$categoryIndex?>)">Previous</button>
                                    <?php endif;?>
                                    <?php if ($category !== 'School and Intervention History'): ?>
                                        <button type="button" onclick="nextCategory(<?=$categoryIndex?>)">Next</button>
                                    <?php endif;?>
                                    <?php if ($category === 'School and Intervention History'): ?>
                                        <button style="background-color: #EBEBEB" type="button" onclick="nextCategory(<?=$categoryIndex?>)" disabled>Next</button>
                                    <?php endif;?>
                                </div>

                        </div>
                        <?php $categoryIndex++;?>
                    <?php endforeach;?>

                    <button type="submit" style="display:none; margin-right: auto; margin-left: auto;" id="submit-button">Submit</button>
                </form>
            </div>
        </section>


        <!-- Why take the Pre-Screening Checklist -->
        <section class="why-pre-screening">
            <h3>Why take the Pre-Screening Checklist?</h3>
            <div class="benefits">
                <div class="benefit">
                    <i class="fa-regular fa-square-check fa-2x"></i> <!-- Font Awesome icon for "Better Outcomes" -->
                    <h4>Better Outcomes</h4>
                    <p>Early identification of your needs and challenges can lead to more effective therapy and quicker progress.</p>
                </div>
                <div class="benefit">
                    <i class="fa-regular fa-clock fa-2x"></i> <!-- Font Awesome icon for "Efficient Use of Time" -->
                    <h4>Efficient Use of Time</h4>
                    <p>By providing information upfront, we can streamline your initial consultation, focusing on areas that matter most to you.</p>
                </div>
                <div class="benefit">
                    <i class="fa-regular fa-heart fa-2x"></i> <!-- Font Awesome icon for "Personalized Care" -->
                    <h4>Personalized Care</h4>
                    <p>The checklist helps us understand your specific needs and concerns, allowing us to tailor our services to you.</p>
                </div>
            </div>
        </section>


        <!-- Modal for Success Message -->
        <div id="success-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Success!</h2>
                <p>Your form was submitted successfully!</p>
                <p>Please note that this form is intended for children who have already been diagnosed by a developmental pediatrician. It serves as part of the enrollment process for therapy services.</p>
                <p>Once submitted, your form will be reviewed by the clinic’s admin team to assess the availability of a therapist for the therapy service you have requested. If a slot is available, you will receive an email with further details and instructions. If no slots are currently available, you will also be notified via email.</p>
                <p>Thank you for your patience as we review your submission!</p>
                <form id="contact-form" action="guestSendPDF.php" method="POST">

                    <!-- Add hidden input for responseID -->
                    <input type="hidden" name="responseID" id="responseID" value="">
                    <button type="submit">Download PDF</button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
        <footer class="footer">
            <div class="footer-container">
                <div class="footer-logo">
                    <img src="images/TheraPeace Logo.svg" alt="TheraPeace Logo">
                    <h2>TheraPeace</h2>
                    <p>Your Partner in Patient Care</p>
                </div>
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#rates">Rates</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Contact Us</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> Polytechnic University of the Philippines</li>
                        <li><i class="fas fa-phone-alt"></i> +63 123 456 7890</li>
                        <li><i class="fas fa-envelope"></i> contact@therapeace.com</li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h3>Follow Us</h3>
                    <ul class="social-icons">
                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 TheraPeace. All Rights Reserved.</p>
            </div>
        </footer>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <script>
                window.onload = function() { showModal(); };
            </script>
        <?php endif;?>

        <script src="guestScript.js"></script>
</div>
</body>
</html>
