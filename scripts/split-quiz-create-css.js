/**
 * One-off: split enp_quiz-create.css into partials by line range.
 * Run from plugin root: node scripts/split-quiz-create-css.js
 */

const fs = require('fs');
const path = require('path');

const cssPath = path.join(__dirname, '../public/quiz-create/css/enp_quiz-create.css');
const partsDir = path.join(__dirname, '../public/quiz-create/css/parts');
const css = fs.readFileSync(cssPath, 'utf8');
const lines = css.split('\n');

/* Line ranges are 1-based, end inclusive. */
const sections = [
  { start: 21, end: 88, file: 'setup.css' },
  { start: 89, end: 312, file: 'utilities.css' },
  { start: 313, end: 436, file: 'typography.css' },
  { start: 437, end: 568, file: 'base.css' },
  { start: 569, end: 836, file: 'animations.css' },
  { start: 837, end: 1039, file: 'forms.css' },
  { start: 1040, end: 2691, file: 'quiz-create.css' },
  { start: 2692, end: 2812, file: 'ab-create.css' },
  { start: 2813, end: 3496, file: 'dashboard.css' },
  { start: 3497, end: 3930, file: 'quiz-results.css' },
  { start: 3931, end: lines.length, file: 'ab-results.css' },
];

if (!fs.existsSync(partsDir)) {
  fs.mkdirSync(partsDir, { recursive: true });
}

for (const { start, end, file } of sections) {
  const block = lines.slice(start - 1, end).join('\n').trim();
  const outPath = path.join(partsDir, file);
  fs.writeFileSync(outPath, block + '\n');
  console.log('Wrote', file);
}

/* Main file: only :root (lines 1-19) */
const rootBlock = lines.slice(0, 19).join('\n').trim();
fs.writeFileSync(cssPath, rootBlock + '\n');
console.log('Updated enp_quiz-create.css (root only)');
