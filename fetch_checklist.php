<!-- fetch_checklist.php -->
<?php
include 'db_conn.php';

$guestID = isset($_GET['guestID']) ? (int)$_GET['guestID'] : 0;

if ($guestID > 0) {
    // Use guestID as responseID since they are linked
    $responseID = $guestID;

    // Step 2: Retrieve questions and answers for the checklist
    $sql = "
        SELECT pq.questionID, pq.category, pq.questionText, pq.options, pq.inputType, fa.answerText
        FROM prescreening_questions pq 
        LEFT JOIN form_answers fa ON pq.questionID = fa.questionID AND fa.responseID = ?
        ORDER BY pq.category
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $responseID);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $optionsArray = explode(',', $row['options']);
            $questions[$row['category']][] = [
                'questionID' => $row['questionID'],
                'questionText' => $row['questionText'],
                'options' => $optionsArray,
                'inputType' => $row['inputType'],
                'selectedAnswer' => $row['answerText'],
            ];
        }
    }

    // Display questions grouped by category
    foreach ($questions as $category => $question_list): ?>
        <div class="checkbox-group">
            <div class="section-title"><?php echo htmlspecialchars($category); ?></div>
            <?php foreach ($question_list as $item): ?>
                <div class="question">
                    <span style="font-weight: 600; margin-bottom:10px; font-size: 14px;">
                        <?php echo htmlspecialchars($item['questionText']); ?>
                    </span>
                    
                    <?php if ($item['inputType'] == 'checkbox') { ?>
                        <?php foreach ($item['options'] as $option): 
                            $isChecked = ($item['selectedAnswer'] == $option); ?>
                            <label class="styled-checkbox">
                                <input type="checkbox" <?php echo $isChecked ? 'checked' : ''; ?> disabled>
                                <?php echo htmlspecialchars($option); ?>
                            </label>
                        <?php endforeach; ?>
                    <?php } elseif ($item['inputType'] == 'radio') { ?>
                        <?php foreach ($item['options'] as $option): 
                            $isChecked = ($item['selectedAnswer'] == $option); ?>
                            <label class="styled-radio">
                                <input type="radio" name="<?php echo htmlspecialchars($item['questionText']); ?>" <?php echo $isChecked ? 'checked' : ''; ?> disabled>
                                <?php echo htmlspecialchars($option); ?>
                            </label>
                        <?php endforeach; ?>
                    <?php } else { 
                        $selectedAnswer = htmlspecialchars($item['selectedAnswer'] ?? 'No answer provided'); ?>
                        <div class="answer-text" style="margin-top: 5px; font-size: 14px; color: #432705;">
                            <?php echo $selectedAnswer; ?>
                        </div>
                    <?php } ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; 
} else {
    echo "Invalid guest ID.";
}
?>
