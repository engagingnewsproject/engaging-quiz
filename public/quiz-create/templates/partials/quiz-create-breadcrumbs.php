<?
// we need to set a few states here to figure out what links are allowed
// And what those links should be
$enp_create_url = ENP_QUIZ_CREATE_URL.$quiz->get_quiz_id().'/';
$enp_create_class = ($enp_current_page === 'create' ? ' enp-quiz-breadcrumbs__link--active' : ' enp-quiz-breadcrumbs__link--disabled');
$enp_preview_url = ENP_QUIZ_PREVIEW_URL.$quiz->get_quiz_id().'/';
$enp_preview_class = ($enp_current_page === 'preview' ? ' enp-quiz-breadcrumbs__link--active' : ' enp-quiz-breadcrumbs__link--disabled');
$enp_publish_url = ENP_QUIZ_PUBLISH_URL.$quiz->get_quiz_id().'/';
$enp_publish_class = ($enp_current_page === 'publish' ? ' enp-quiz-breadcrumbs__link--active' : ' enp-quiz-breadcrumbs__link--disabled');

?>


<nav class="enp-quiz-breadcrumbs">
    <ul class="enp-quiz-breadcrumbs__list">
        <li class="enp-quiz-breadcrumbs__item">
            <a
                href="<?php echo $enp_create_url;?>"
                class="enp-quiz-breadcrumbs__link<?php echo $enp_create_class;?>">
                    Create</a>
        </li>
        <li class="enp-quiz-breadcrumbs__item"><svg class="enp-icon">
         <use xlink:href="#icon-chevron-right" />
        </svg></li>
        <li class="enp-quiz-breadcrumbs__item"><a class="enp-quiz-breadcrumbs__link enp-quiz-breadcrumbs__link--preview<?php echo $enp_preview_class;?>"  href="<? echo $enp_preview_url;?>">Preview</a></li>
        <li class="enp-quiz-breadcrumbs__item"><svg class="enp-icon">
         <use xlink:href="#icon-chevron-right" />
        </svg></li>
        <li class="enp-quiz-breadcrumbs__item"><a class="enp-quiz-breadcrumbs__link<?php echo $enp_publish_class;?>" href="<? echo $enp_publish_url;?>">Publish</a></li>
    </ul>
</nav>
