# Contributing to Astro Animations

Thank you for your interest in contributing to **Astro Animations**! We welcome bug reports, feature suggestions, documentation improvements, and new animation presets from creators and developers worldwide.

---

## Code of Conduct
We are committed to providing a welcoming, inclusive, and harassment-free environment for everyone. Please be respectful and constructive in all interactions.

---

## How Can I Contribute?

### 1. Reporting Bugs
- Check the [Issues tab](https://github.com/ferhat/astro-animations/issues) to ensure the bug hasn't already been reported.
- Use the **Bug Report** template.
- Include clear steps to reproduce, your WordPress and PHP versions, and browser console errors (if any).

### 2. Suggesting Enhancements & Presets
- Have an idea for a new interactive animation, simulation, or UI component?
- Open a **Feature Request** issue describing the visual effect, responsive behavior, and use case.

### 3. Pull Requests
1. Fork the repository and create your branch from `main`:
   ```bash
   git checkout -b feature/my-cool-animation
   ```
2. Ensure your code adheres to [WordPress Coding Standards (WPCS)](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/).
3. Make sure all strings are wrapped in internationalization functions using the `'astro-animations'` text domain.
4. Verify responsiveness across desktop and mobile devices.
5. Ensure zero Cumulative Layout Shift (CLS) on page loads.
6. Submit your pull request with a descriptive title and detailed summary.

---

## Development Guidelines
- **PHP**: Minimum PHP 7.4 compatibility. Always escape outputs (`esc_html`, `esc_attr`, `esc_url`).
- **CSS**: Prefix custom classes where appropriate, and always ensure containers specify `box-sizing: border-box`.
- **JavaScript**: Wrap client scripts in IIFE closures to prevent global scope contamination.

Thank you for helping make Astro Animations better!
