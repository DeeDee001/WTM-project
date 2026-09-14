from pathlib import Path
import re

base = Path(r'c:\xampp\htdocs\wtm\arafat')
view_dir = base / 'view'
css_dir = base / 'css'

for view_path in sorted(view_dir.glob('*.php')):
    text = view_path.read_text(encoding='utf-8')
    match = re.search(r'(?is)<style[^>]*>(.*?)</style>', text)
    if not match:
        continue

    css_name = f'{view_path.stem}.css'
    css_file = css_dir / css_name
    css_content = match.group(1).strip()
    css_file.write_text(css_content + '\n', encoding='utf-8')

    link = (
        f'<link rel="stylesheet" href="<?php echo (strpos($_SERVER[\'REQUEST_URI\'] ?? \'\', \'/view/\') !== false ? \'../css/{css_name}\' : \'css/{css_name}\'); ?>">'
    )

    text = text[:match.start()] + link + text[match.end():]
    view_path.write_text(text, encoding='utf-8')
    print(f'Updated {view_path.name} -> {css_name}')
