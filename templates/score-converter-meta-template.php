<?php
    global $template_data;
    global $scoring_chart;
    global $totalQuestionRows;

    global $total_raw_questions;
    global $min_scored_value;
    global $max_scored_value;

    extract($template_data);
?>

<input type="hidden" name="score_converter_meta_nonce" value="<?php echo $score_converter_meta_nonce; ?>" />

<table  id="score_range">
    <tr>
        <td>Total Questions:</td>
        <td><input type="text" name="total_raw_questions" value="<?php echo $total_raw_questions ?>" /></td>
    </tr>

    <tr>
        <td>Minimum Converted Score:</td><td><input type="text" name="minimum_scaled_score" value="<?php echo $min_scored_value ?>" /></td>
    </tr>
    <tr>
        <td>Maximum Converted Score:</td><td> <input type="text" name="maximum_scaled_score" value="<?php echo $max_scored_value ?>" /></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="initialize_scoring_formula" id="initialize_scoring_formula"  value="Initialize Scoring Table" /></td>
    </tr>

</table>

<table class="form-table">
    <tr>
        <th colspan="4">Raw Score to Scaled Score Conversion Charts</label></th>
    </tr>
    <tr>
        <th>RAW SCORE<br/>(# of correct answer)</th>
        <th>SAT<br/> TEST SCORE</th>
        <th>RAW SCORE<br/>(# of correct answer)</th>
        <th>SAT<br/> TEST SCORE</th>
    </tr>
    <?php
    $j = 0;
    $inputTabIndex = 100;
    for ($i = 0; $i < $totalQuestionRows; ++$i) {
        ?>

        <tr>
            <td>
                <label> <?php echo $i; ?></label>
            </td>
            <td>
                <input
                       name='<?php echo $scoring_chart[$i]['name']; ?>'
                       id='<?php echo $scoring_chart[$i]['id']; ?>'
                       type='text'
                       value= '<?php echo $scoring_chart[$i]['value']; ?>'
                       tabindex='<?php echo $inputTabIndex+$i; ?>'
                />
            </td>
            <?php
            $j = $i + $totalQuestionRows;
            if ($j <= $total_raw_questions ) {
            ?>
            <td>
                <label> <?php echo $j; ?></label>
            </td>
            <td>
                <input
                    name='<?php echo $scoring_chart[$j]['name']; ?>'
                    id='<?php echo $scoring_chart[$j]['id']; ?>'
                    type='text'
                    value= '<?php echo $scoring_chart[$j]['value']; ?>'
                    tabindex='<?php echo $inputTabIndex+$j; ?>'
                />
            </td>
            <?php } else {  ?>
                <td></td>
                <td></td>
            <?php }  ?>
        </tr>

    <?php }  ?>


</table>
