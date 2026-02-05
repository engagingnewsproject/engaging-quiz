/**
 * One-off: prepend :root with CSS variables and replace design-token hex with var().
 * Reads *-expanded.css (from sass compile), writes the new source .css.
 */

const fs = require('fs');
const path = require('path');

const QUIZ_CREATE_ROOT = `:root {
  /* Design tokens (from SCSS variables) */
  --font: #1d1c25;
  --background: #FAF9FB;
  --title: #5D5E5F;
  --blue: #00A9B7;
  --dark-blue: #333F48;
  --green: #60AAAD;
  --red: #bf5700;
  --link: #bf5700;
  --khaki: #CCA562;
  --light-gray: #FAF9FB;
  --font-body: Arial, monospace, helvetica, arial, sans-serif;
  --font-title: Arial, monospace, helvetica, arial, sans-serif;
  --ease: cubic-bezier(0, 0, 0.3, 1);
  --breakpoint-small: 400px;
  --breakpoint-medium: 700px;
  --breakpoint-large: 1000px;
}

`;

const QUIZ_TAKE_ROOT = `:root {
  /* Design tokens (from SCSS variables) - quiz-take uses blue as link */
  --font: #1d1c25;
  --background: #FAF9FB;
  --title: #5D5E5F;
  --blue: #00A9B7;
  --dark-blue: #333F48;
  --green: #60AAAD;
  --red: #bf5700;
  --link: #00A9B7;
  --khaki: #CCA562;
  --light-gray: #FAF9FB;
  --font-body: Arial, monospace, helvetica, arial, sans-serif;
  --font-title: Arial, monospace, helvetica, arial, sans-serif;
  --ease: cubic-bezier(0, 0, 0.3, 1);
  --breakpoint-small: 400px;
  --breakpoint-medium: 700px;
  --breakpoint-large: 1000px;
}

`;

function replaceHexWithVars(css, linkIsRed) {
  const replacements = [
    [/#1d1c25/gi, 'var(--font)'],
    [/#FAF9FB/g, 'var(--background)'],
    [/#faf9fb/gi, 'var(--background)'],
    [/#5D5E5F/gi, 'var(--title)'],
    [/#00A9B7/gi, 'var(--blue)'],
    [/#00a9b7/gi, 'var(--blue)'],
    [/#60AAAD/gi, 'var(--green)'],
    [/#60aaad/gi, 'var(--green)'],
    [/#333F48/gi, 'var(--dark-blue)'],
    [/#333f48/gi, 'var(--dark-blue)'],
    [/#CCA562/gi, 'var(--khaki)'],
    [/#cca562/gi, 'var(--khaki)'],
  ];
  if (linkIsRed) {
    replacements.push([/#bf5700/gi, 'var(--link)']);
  } else {
    replacements.push([/#bf5700/gi, 'var(--red)']);
  }
  let out = css;
  for (const [hex, v] of replacements) {
    out = out.replace(hex, v);
  }
  return out;
}

const pluginRoot = path.join(__dirname, '..');

// Quiz-create
const createExpanded = path.join(pluginRoot, 'public/quiz-create/css/enp_quiz-create-expanded.css');
const createOut = path.join(pluginRoot, 'public/quiz-create/css/enp_quiz-create.css');
let createCss = fs.readFileSync(createExpanded, 'utf8');
createCss = QUIZ_CREATE_ROOT + replaceHexWithVars(createCss, true);
fs.writeFileSync(createOut, createCss);
console.log('Wrote', createOut);

// Quiz-take
const takeExpanded = path.join(pluginRoot, 'public/quiz-take/css/enp_quiz-take-expanded.css');
const takeOut = path.join(pluginRoot, 'public/quiz-take/css/enp_quiz-take.css');
let takeCss = fs.readFileSync(takeExpanded, 'utf8');
takeCss = QUIZ_TAKE_ROOT + replaceHexWithVars(takeCss, false);
fs.writeFileSync(takeOut, takeCss);
console.log('Wrote', takeOut);
