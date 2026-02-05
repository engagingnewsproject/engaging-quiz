# Quiz Take CSS parts

These files are concatenated in a fixed order by gulp to produce `enp_quiz-take.min.css`. Order matches the former SCSS partials in `sass/enp_quiz-take.scss`.

**Order** (do not reorder in gulpfile): setup → utilities → typography → forms → animations → quiz → slider.

- **setup.css** – reset, html/body/#enp-quiz base, box-sizing, iframe
- **utilities.css** – screen-reader text, quiz messages (error/success)
- **typography.css** – body, headings, text elements, links, buttons, fieldset reset, .enp-page-title
- **forms.css** – .enp-label, .enp-legend, .enp-input, placeholders
- **animations.css** – @keyframes (removeAnswers, removeQuestion, showNextQuestion, slideInTop/Bottom, etc.), .spinner
- **quiz.css** – quiz container, header, progress, questions, options, submit, explanation, results, share, modal, callout
- **slider.css** – slider input, range, handle, jQuery UI slider styles

Design tokens live in `../enp_quiz-take.css` (`:root` only). There is no **structure.css** – the SCSS `_structure.scss` only defined mixins (no CSS output).
