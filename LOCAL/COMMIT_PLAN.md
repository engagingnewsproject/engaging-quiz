# Commit plan for feature/mc-images (matches your git status)

Run from plugin root:  
`cd /Users/lukey/Sites/mediaengagement/app/public/wp-content/plugins/enp-quiz`

Do commits in order. Skip or split a commit if you know some files belong elsewhere.

---

## 1. Database: mc_option_image + upgrade 1.2.0

```bash
git add enp_quiz.php includes/class-enp_quiz-activator.php upgrade.php
git commit -m "add mc_option_image to db and upgrade to 1.2.0"
```

---

## 2. Model: MC option + question (image support)

```bash
git add includes/class-enp_quiz-mc_option.php includes/class-enp_quiz-question.php
git commit -m "mc option model: add image property and getter"
```

---

## 3. Save layer: MC option image upload/delete

```bash
git add database/class-enp_quiz_save_mc_option.php database/class-enp_quiz_save_question.php database/class-enp_quiz_save_quiz_response.php
git commit -m "save mc option image uploads and delete action"
```

---

## 4. Quiz create: MC option image UI (templates, JS, CSS, SVG)

```bash
git add public/quiz-create/templates/partials/quiz-create-mc-option.php \
        public/quiz-create/templates/partials/quiz-create-question.php \
        public/quiz-create/js/quiz-create/quiz-create--ux.js \
        public/quiz-create/js/quiz-create/quiz-create--save.js \
        public/quiz-create/js/quiz-create/quiz-create--onLoad.js \
        public/quiz-create/js/quiz-create/quiz-create--utilities.js \
        public/quiz-create/css/sass/_quiz-create.scss \
        public/quiz-create/css/sass/_forms.scss \
        public/quiz-create/svg/symbol-defs.svg \
        public/quiz-create/class-enp_quiz-create.php \
        public/quiz-create/includes/class-enp_quiz-quiz_create.php
git add public/quiz-create/svg/icon-image.svg
git commit -m "quiz create: mc option image upload, preview, hide until 3 chars"
```

---

## 5. Quiz take: MC option images + expand modal

```bash
git add public/quiz-take/templates/partials/mc-option.php \
        public/quiz-take/templates/quiz.php \
        public/quiz-take/js/quiz-take/quiz-take--ux.js \
        public/quiz-take/js/quiz-take/quiz-take--mc-option.js \
        public/quiz-take/js/quiz-take/quiz-take--question-explanation.js \
        public/quiz-take/js/quiz-take/quiz-take--question.js \
        public/quiz-take/js/quiz-take/quiz-take--templates.js \
        public/quiz-take/includes/class-enp_quiz-take_question.php \
        public/quiz-take/css/sass/_quiz.scss \
        public/quiz-take/svg/symbol-defs.svg
git commit -m "quiz take: mc option images, expand modal (.attr fix)"
```

---

## 6. Build: quiz-create + quiz-take compiled assets

```bash
git add public/quiz-create/css/enp_quiz-create.min.css public/quiz-create/css/enp_quiz-create.min.css.map \
        public/quiz-create/js/dist/quiz-create.js public/quiz-create/js/dist/quiz-create.min.js \
        public/quiz-create/js/dist/ab-results.min.js \
        public/quiz-create/js/dist/dashboard.js public/quiz-create/js/dist/dashboard.min.js \
        public/quiz-create/js/dist/quiz-results.min.js
git add public/quiz-take/css/enp_quiz-take.min.css public/quiz-take/css/enp_quiz-take.min.css.map \
        public/quiz-take/js/dist/quiz-take.js public/quiz-take/js/dist/quiz-take.min.js \
        public/quiz-take/js/dist/iframe-parent.js public/quiz-take/js/dist/iframe-parent.min.js \
        public/quiz-take/js/dist/utilities.min.js
git commit -m "build: quiz-create and quiz-take assets"
```

---

## 7. Tooling / other (optional — commit or leave for later)

If these are part of this branch and you want them committed:

```bash
git add gulpfile.js package.json public/quiz-create/js/dashboard.js public/quiz-create/js/utilities/display-messages.js
git commit -m "gulp/package and misc quiz-create js"
```

Untracked: `COMMIT_PLAN.md`, `package-lock.json` — add only if you want them in the repo (e.g. `git add COMMIT_PLAN.md` or add `COMMIT_PLAN.md` to `.gitignore`).

---

## Push

```bash
git push origin feature/mc-images
```
