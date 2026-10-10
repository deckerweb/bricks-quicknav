from pathlib import Path
import json
root=Path(__file__).resolve().parents[1]
(root/'docs').mkdir(exist_ok=True);(root/'wiki').mkdir(exist_ok=True)
content=json.loads((root/'docs/content.json').read_text())
history=json.loads((root/'includes/history.json').read_text())
for lang,c in content.items():
 de=lang=='de';other='README.md' if de else 'README-de.md';otherlabel='English' if de else 'Deutsch'
 titles=c['titles'];labels={'new':'Neu' if de else 'New','improved':'Verbessert' if de else 'Improved','fixed':'Behoben' if de else 'Fixed','misc':'Sonstiges' if de else 'Misc'}
 changes='\n\n'.join('### '+r['version']+' · '+r['date'][lang]+'\n\n'+'\n'.join('- **'+labels[i['category']]+':** '+i[lang] for i in r['items']) for r in history)
 faq='\n\n'.join('### '+q+'\n\n'+a for q,a in c['faq'])
 features='\n\n'.join('### '+t+'\n\n'+v for t,v in c['features'])
 anchors=['about','overview','installation','features','faq','changelog','support']
 toc='\n'.join('- ['+t+'](#'+a+')' for t,a in zip(titles,anchors))
 body='# Bricks QuickNav\n\n![Bricks QuickNav](assets/banner-'+lang+'-1280x640.png)\n\n['+otherlabel+']('+other+')\n\n'+c['about']+'\n\n'+c['requirements']+'\n\n[GitHub Wiki](https://github.com/deckerweb/bricks-quicknav/wiki) · [FAQ](wiki/'+('Fragen-nach-Themen.md' if de else 'FAQ-by-topic.md')+')\n\n'+toc+'\n\n'
 sections=[c['about'], '\n'.join('- '+t for t,v in c['features']),('Installiere das Plugin-ZIP über Plugins → Installieren → Plugin hochladen. Aktiviere das Plugin und öffne die Einstellungsseite. Vor dem Upgrade von 1.0.0 eigene Konstanten und Filter prüfen. Die native Toolbar-Sichtbarkeit wird nach dem Upgrade von WordPress beziehungsweise Bricks bestimmt.' if de else 'Install the plugin ZIP through Plugins → Add Plugin → Upload Plugin. Activate it and open its settings page. Before upgrading from 1.0.0, review your constants and filters. Native toolbar visibility after the upgrade is controlled by WordPress and Bricks.'),features,faq,changes,c['support']+'\n\n'+c['credits']+'\n\n© 2025–2026 David Decker – DECKERWEB.']
 for t,a,v in zip(titles,anchors,sections):body+='<a id="'+a+'"></a>\n\n## '+t+'\n\n'+v+'\n\n'
 (root/('README-de.md' if de else 'README.md')).write_text(body.rstrip()+'\n')
 (root/'wiki'/('Startseite.md' if de else 'Home.md')).write_text(body.replace('(assets/','(https://raw.githubusercontent.com/deckerweb/bricks-quicknav/main/assets/').replace('('+other+')','(Startseite)' if not de else '(Home)'))
 (root/'wiki'/('Fragen-nach-Themen.md' if de else 'FAQ-by-topic.md')).write_text('# '+('Fragen nach Themen' if de else 'FAQ by topic')+'\n\n'+faq+'\n\n'+features+'\n')
 (root/'wiki'/('Aenderungsverlauf.md' if de else 'Changelog.md')).write_text('# '+titles[5]+'\n\n'+changes+'\n')
 (root/('SECURITY-de.md' if de else 'SECURITY.md')).write_text('# '+('Sicherheitsmeldungen' if de else 'Security reporting')+'\n\n'+('Bitte Sicherheitslücken privat über den folgenden GitHub-Meldeweg einreichen. Host-, Library- und Updater-Version, Umgebung, Reproduktion und Auswirkungen nennen; keine Zugangsdaten oder personenbezogenen Inhalte mitsenden. Normale Fehler gehören in Issues. Falls der private GitHub-Meldeweg nicht verfügbar ist, keine Sicherheitsdetails öffentlich posten.' if de else 'Report vulnerabilities privately through the GitHub reporting link below. Include host, Library and Updater versions, environment, reproduction, and impact; omit credentials and personal content. Ordinary bugs belong in Issues. If private GitHub reporting is unavailable, do not post security details publicly.')+'\n\n[Private report](https://github.com/deckerweb/bricks-quicknav/security/advisories/new)\n\n['+otherlabel+']('+('SECURITY.md' if de else 'SECURITY-de.md')+')\n')
 if not de:
  readme='=== Bricks QuickNav ===\nContributors: deckerweb\nTags: bricks, toolbar, navigation\nRequires at least: 6.7\nTested up to: 7.1\nRequires PHP: 7.4\nStable tag: 2.0.0\nLicense: GPLv2 or later\nLicense URI: https://www.gnu.org/licenses/gpl-2.0.html\n\n'+c['about']+'\n\n== Description ==\n'+c['requirements']+'\n\n'+'\n\n'.join('= '+t+' =\n'+v for t,v in c['features'])+'\n\n== Installation ==\n'+sections[2]+'\n\n== Frequently Asked Questions ==\n'+'\n\n'.join('= '+q+' =\n'+a for q,a in c['faq'])+'\n\n== Changelog ==\n'+'\n\n'.join('= '+r['version']+' =\n'+'\n'.join('* '+labels[i['category']]+': '+i[lang] for i in r['items']) for r in history)+'\n\n== Upgrade Notice ==\n= 2.0.0 =\nUses native toolbar visibility. Existing constants, resources, and integrations remain supported.\n'
  (root/'readme.txt').write_text(readme)
print('Synchronized EN/DE readmes, FAQ, history, security guidance and wiki sources generated.')
