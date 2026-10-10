from pathlib import Path
import re, gettext, json, struct
root=Path(__file__).resolve().parents[1]
dictionary=json.loads((root/'languages/dictionary.json').read_text())
extra={
 'Published':'Veröffentlicht', 'Unpublished':'Nicht veröffentlicht',
 'Frameworks':'Frameworks', 'Plugin Website':'Plugin-Website',
 'Fewer clicks. More Bricks.':'Weniger Klicks. Mehr Bricks.',
 'Bricks is not active. QuickNav settings remain available; toolbar navigation resumes when a supported builder is active.':'Bricks ist nicht aktiv. Die QuickNav-Einstellungen bleiben erreichbar; die Toolbar-Navigation erscheint wieder, sobald Bricks aktiv ist.',
 'Template types':'Template-Typen', 'Template bundles and tags':'Template-Bündel und Schlagwörter', 'Compact menu':'Kompaktes Menü', 'Toolbar position':'Toolbar-Position', 'Brick icon':'Ziegel-Icon', 'Code':'Code', 'Not active':'Nicht aktiv', 'Active':'Aktiv',
 'Quick access to Bricks content, templates, settings, add-ons, and resources from the WordPress toolbar.':'Schneller Zugriff auf Bricks-Inhalte, Templates, Einstellungen, Addons und Ressourcen über die WordPress-Toolbar.',
 'Navigation remains available. The bundled Library needs PHP 8.0; the updater integration needs PHP 8.1 and a compatible deckerweb Updater. Update manually while a component is unavailable.':'Die Navigation bleibt verfügbar. Die enthaltene Library benötigt PHP 8.0; die Update-Anbindung benötigt PHP 8.1 und einen kompatiblen deckerweb Updater. Aktualisiere manuell, wenn eine Komponente nicht verfügbar ist.',
 'Installed but inactive':'Installiert, aber inaktiv', 'Not installed':'Nicht installiert',
 'Template-type lists use the same bounded template selection as the main template list. Increase the item limit to include more templates.':'Die Template-Typen verwenden dieselbe begrenzte Auswahl wie die Template-Liste. Erhöhe die Anzahl der Einträge, um weitere Templates einzubeziehen.',
}
strings=set()
literal=r"'((?:\\.|[^'\\])*)'"
for p in [root/'bricks-quicknav.php',*list((root/'includes').glob('*.php'))]:
 s=p.read_text()
 for m in re.finditer(r'(?:esc_html__|esc_attr__|esc_html_e|esc_attr_e|__|_e)\(\s*'+literal+r"\s*,\s*'bricks-quicknav'",s):strings.add(m[1].replace("\\'","'").replace('\\\\','\\'))
 for m in re.finditer(r'(?:esc_html_x|esc_attr_x|_x)\(\s*'+literal+r'\s*,\s*'+literal+r"\s*,\s*'bricks-quicknav'",s):strings.add(m[2].replace("\\'","'")+'\x04'+m[1].replace("\\'","'"))
strings.add('Quick access to Bricks content, templates, settings, add-ons, and resources from the WordPress toolbar.')
messages=json.loads((root/'includes/deckerweb-plugin-library/messages.json').read_text());strings.update(messages)
def port(s):return s.replace('Breakdance','Bricks').replace('breakdance-quicknav','bricks-quicknav').replace('Breakdance 2 & 3','Bricks')
def mo(entries):
 keys=sorted(entries);n=len(keys);offset=28+16*n;orig=b'';trans=b'';ot=[];tt=[]
 for k in keys:
  b=k.encode();ot.append((len(b),offset+len(orig)));orig+=b+b'\0'
 offset+=len(orig)
 for k in keys:
  b=entries[k].encode();tt.append((len(b),offset+len(trans)));trans+=b+b'\0'
 return struct.pack('<7I',0x950412de,0,n,28,28+8*n,0,0)+b''.join(struct.pack('<2I',*x) for x in ot+tt)+orig+trans
q=lambda s:json.dumps(s,ensure_ascii=False)
def entry(k,v):
 if '\x04' in k:
  ctx,k=k.split('\x04');return 'msgctxt '+q(ctx)+'\nmsgid '+q(k)+'\nmsgstr '+q(v)
 return 'msgid '+q(k)+'\nmsgstr '+q(v)
outdict={}
for lang in ['de_DE','de_DE_formal']:
 mapping=dict(dictionary[lang])
 mapping.update({k:v[lang] for k,v in messages.items()})
 mapping.update(extra)
 if lang=='de_DE_formal':
  for k in extra: mapping[k]=mapping[k].replace('Deine Verknüpfungen','Ihre Verknüpfungen').replace('Aktualisiere manuell','Aktualisieren Sie manuell').replace('Erhöhe die Anzahl','Erhöhen Sie die Anzahl')
 missing=strings-set(mapping)
 if missing: raise SystemExit('Missing '+lang+': '+json.dumps(sorted(missing),ensure_ascii=False))
 header='Project-Id-Version: Bricks QuickNav 2.0.0\nContent-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit\nMIME-Version: 1.0\nLanguage: '+lang+'\nPlural-Forms: nplurals=2; plural=(n != 1);\n'
 catalog={k:mapping[k] for k in strings};catalog['']=header
 (root/'languages'/('bricks-quicknav-'+lang+'.mo')).write_bytes(mo(catalog))
 (root/'languages'/('bricks-quicknav-'+lang+'.po')).write_text('\n\n'.join(entry(k,v) for k,v in sorted(catalog.items()))+'\n')
 outdict[lang]={k:v for k,v in catalog.items() if k}
 # Do not leave an obsolete PHP translation cache next to the new MO catalog.
 cache=root/'languages'/('bricks-quicknav-'+lang+'.l10n.php')
 if cache.exists():cache.unlink()
(root/'languages/bricks-quicknav.pot').write_text(entry('',header)+'\n\n'+'\n\n'.join(entry(k,'') for k in sorted(strings))+'\n')
(root/'languages/dictionary.json').write_text(json.dumps(outdict,ensure_ascii=False,indent=2)+'\n')
print(len(strings),'host messages compiled for both German locales.')
