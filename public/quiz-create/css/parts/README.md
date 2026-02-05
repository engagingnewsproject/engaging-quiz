# Quiz Create CSS parts

These files are concatenated in a fixed order by gulp to produce `enp_quiz-create.min.css`. Order matches the former SCSS partials in `sass/enp_quiz-create.scss`.

**Order** (do not reorder in gulpfile): setup → utilities → typography → base → animations → forms → quiz-create → quiz-preview → quiz-publish → ab-create → dashboard → breadcrumbs → quiz-results → ab-results → slider.

- **setup.css** – html/body/#enp-quiz reset, box-sizing
- **utilities.css** – screen-reader, accordion, messages, tooltip, sticky, .enp-breadcrumb-link
- **typography.css** – body, headings, paragraphs, lists, tables
- **base.css** – links, buttons, focus
- **animations.css** – @keyframes
- **forms.css** – labels, inputs, placeholders
- **quiz-create.css** – quiz builder (questions, slider options, accordion, etc.)
- **quiz-preview.css** – preview form, quiz-preview title, iris/quiz-styles
- **quiz-publish.css** – publish page layout, share quiz
- **ab-create.css** – AB test create
- **dashboard.css** – dashboard list, dash items, nav, search
- **breadcrumbs.css** – breadcrumb container, .enp-quiz-breadcrumbs*
- **quiz-results.css** – results flow, score, share
- **ab-results.css** – AB results (winner/loser, etc.)
- **slider.css** – slider UI (from quiz-take; used in create for slider question type)

Design tokens live in `../enp_quiz-create.css` (`:root` only). No **structure.css** – the SCSS _structure.scss only defined mixins (no CSS output).
