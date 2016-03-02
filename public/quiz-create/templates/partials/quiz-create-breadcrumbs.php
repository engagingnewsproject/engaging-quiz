<nav class="enp-quiz-breadcrumbs">
    <ul class="enp-quiz-breadcrumbs__list">
        <li class="enp-quiz-breadcrumbs__item">
            <a
                href="quiz-add-question.php"
                class="enp-quiz-breadcrumbs__link <? echo (strpos( $URL,'quiz-add-question.php') ? 'enp-quiz-breadcrumbs__link--active' : '');?>">
                    Create</a>
        </li>
        <li class="enp-quiz-breadcrumbs__item"><svg class="enp-icon">
         <use xlink:href="#icon-chevron-right" />
        </svg></li>
        <? $preview_classes = ' enp-quiz-breadcrumbs__link--disabled';
            if(strpos( $URL,'quiz-preview.php') || strpos( $URL,'quiz-publish.php')) {
                $preview_classes = '';
            }?>
        <li class="enp-quiz-breadcrumbs__item"><a class="enp-quiz-breadcrumbs__link enp-quiz-breadcrumbs__link--preview <? echo (strpos( $URL,'quiz-preview.php') ? 'enp-quiz-breadcrumbs__link--active' : $preview_classes);?>"  href="quiz-preview.php">Preview</a></li>
        <li class="enp-quiz-breadcrumbs__item"><svg class="enp-icon">
         <use xlink:href="#icon-chevron-right" />
        </svg></li>
        <li class="enp-quiz-breadcrumbs__item"><a class="enp-quiz-breadcrumbs__link <? echo (strpos( $URL,'quiz-publish.php') ? 'enp-quiz-breadcrumbs__link--active' : '');?><? echo (strpos( $URL,'quiz-add-question.php') ? ' enp-quiz-breadcrumbs__link--disabled' : '');?>" href="quiz-publish.php">Publish</a></li>
    </ul>
</nav>
