"""Build the installable host ZIP from explicit runtime paths, never addon sources."""
from pathlib import Path
import sys,zipfile,json,hashlib
root=Path(__file__).resolve().parents[1]
output=Path(sys.argv[1]).resolve();output.parent.mkdir(parents=True,exist_ok=True)
paths=[root/'bricks-quicknav.php',root/'uninstall.php',root/'index.php',root/'README.md',root/'README-de.md',root/'readme.txt',root/'SECURITY.md',root/'SECURITY-de.md',root/'docs/addon-registration.md',root/'docs/addon-registration-de.md',root/'docs/child-theme-registration.md',root/'docs/child-theme-registration-de.md']
for folder in ['includes','assets','languages']:
 paths.extend(p for p in (root/folder).rglob('*') if p.is_file() and p.name!='dictionary.json' and not p.name.startswith('.'))
for name in ['LICENSE','LICENSE.txt','license.txt']:
 if (root/name).is_file():paths.append(root/name)
with zipfile.ZipFile(output,'w',zipfile.ZIP_DEFLATED,compresslevel=9) as z:
 for p in sorted(set(paths)):
  if p.is_symlink():raise ValueError('Symlinks forbidden')
  z.write(p,'bricks-quicknav/'+p.relative_to(root).as_posix())
with zipfile.ZipFile(output) as z:
 assert z.testzip() is None
 for name in z.namelist():
  assert name.startswith('bricks-quicknav/') and '..' not in Path(name).parts
  if 'docs' in Path(name).parts: assert Path(name).name in ['addon-registration.md','addon-registration-de.md','child-theme-registration.md','child-theme-registration-de.md']
  assert not any(s in Path(name).parts for s in ['.git','work','tests','tools','wiki'])
print(json.dumps({'zip':str(output),'files':len(paths),'bytes':output.stat().st_size,'sha256':hashlib.sha256(output.read_bytes()).hexdigest()}))
