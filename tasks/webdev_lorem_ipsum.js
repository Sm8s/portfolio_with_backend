/*
 * Lorem Ipsum Generator
 * This simple script defines a function to generate a specified number of
 * Lorem‑Ipsum paragraphs. Integrate this script into an HTML page with a
 * button and input field to dynamically insert generated text into the DOM.
 */

// Array of example Lorem‑Ipsum sentences; extend this list as needed.
const sentences = [
  'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
  'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
  'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.',
  'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore.',
  'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt.'
];

function generateParagraph() {
  // Generate a paragraph by concatenating random sentences
  const numSentences = Math.floor(Math.random() * 4) + 2; // between 2 and 5 sentences
  let paragraph = '';
  for (let i = 0; i < numSentences; i++) {
    const randomIndex = Math.floor(Math.random() * sentences.length);
    paragraph += sentences[randomIndex] + ' ';
  }
  return paragraph.trim();
}

/**
 * Generates the requested number of paragraphs and returns them as an array of strings.
 * @param {number} count Number of paragraphs to generate
 * @returns {string[]} Array of generated paragraphs
 */
function generateLorem(count) {
  const paragraphs = [];
  for (let i = 0; i < count; i++) {
    paragraphs.push(generateParagraph());
  }
  return paragraphs;
}

// Example usage: generate 3 paragraphs and log them to console
// const result = generateLorem(3);
// console.log(result.join('\n\n'));
