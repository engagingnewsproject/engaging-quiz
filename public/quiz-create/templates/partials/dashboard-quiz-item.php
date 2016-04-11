<?php
    $quiz_id = $quiz->get_quiz_id();
    $quiz_status = $quiz->get_quiz_status();
    // see if we should go to quiz create or preview
    if($quiz_status === 'published') {
        $quiz_link = ENP_QUIZ_PREVIEW_URL;
        $quiz_title = '<a href="'.$quiz_link.$quiz_id.'">'.$quiz->get_quiz_title().'</a>';
        $quiz_primary_action_link = '<a href="'.ENP_QUIZ_RESULTS_URL.$quiz_id.'">Results</a>';
        $quiz_secondary_action_link = '<a href="'.ENP_QUIZ_PUBLISH_URL.$quiz_id.'">Embed</a>';
    } else {
        $quiz_link = ENP_QUIZ_CREATE_URL;
        $quiz_title = '<a href="'.$quiz_link.$quiz_id.'"><span class="enp-screen-reader-text">Edit </span>'.$quiz->get_quiz_title();
        // if you want to add back in the edit pencil icons
        $quiz_title .= ' <svg class="enp-icon enp-dash-item__title__icon"><use xlink:href="#icon-edit" /></svg>';
        $quiz_primary_action_link = '<a href="'.ENP_QUIZ_CREATE_URL.$quiz_id.'">Edit</a>';
        $quiz_secondary_action_link = '<a href="'.ENP_QUIZ_PREVIEW_URL.$quiz_id.'">Preview</a>';
    }

?>

<li class="enp-dash-item enp-dash-item--<?php echo $quiz_status;?>">
    <h3 class="enp-dash-item__title"><?php echo $quiz_title;?></h3>
    <div class="enp-dash-item__controls">
        <div class="enp-dash-item__status"><? echo $quiz_status;?></div>
        <ul class="enp-dash-item__nav">
            <li class="enp-dash-item__nav__item"><?php echo $quiz_primary_action_link;?></li>
            <li class="enp-dash-item__nav__item">
                <?php echo $quiz_secondary_action_link;?></li>
        </ul>
    </div>
</li>
