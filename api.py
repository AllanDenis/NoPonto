DEBUG = True
import urllib
import time
import re
import xmltodict
from pprint import pprint

'''
curl
'''
'''
'http://info.plataforma.cittati.com.br/m3p/embedded/predictionMap?1-1.IBehaviorListener.0-mapPanel&out=false&ta=415&_=1475809622833'
-H 'Cookie: JSESSIONID=12C90DDB166FB4D51A53D4D00D70EEE2; __utma=110878301.544469757.1475806373.1475806373.1475806373.1; __utmc=110878301; __utmz=110878301.1475806373.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)'
-H 'Accept-Encoding: gzip, deflate, sdch'
-H 'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.6,en;q=0.4'
-H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36'
-H 'Accept: application/xml, text/xml, */*; q=0.01'
-H 'Referer: http://info.plataforma.cittati.com.br/m3p/embedded/predictionMap?1'
-H 'X-Requested-With: XMLHttpRequest'
-H 'Connection: keep-alive'
-H 'Wicket-Ajax: true'
-H 'Wicket-Ajax-BaseURL: predictionMap?1' --compressed
'''
if DEBUG: print("Carregando linhas...")
linhasXml = ''
with open('tmp/linhas.xml') as arquivo:
    linhasXml = arquivo.read()

if DEBUG: print("Tratando arquivo...")
linhasXml = re.sub(r'(<!\[CDATA\[)|(\]\])|(--)','',linhasXml)
linhasXml = re.sub(r'\n\t?','#',linhasXml)
linhasXml = re.sub(r'optgroup','linha',linhasXml)
linhasXml = re.sub(r'option','viagens',linhasXml)
linhasXml = re.sub(r'\( +','(',linhasXml)
linhasXml = re.sub(r'\) +',')',linhasXml)
linhasXml = re.sub(r'( +[xX/] +)',' / ',linhasXml)
filtro = re.compile(r'<linha.*/linha>')
linhasXml = re.findall(filtro,linhasXml)
linhasXml = linhasXml[0].split('#')
linhasXml = '<xml>\n'+'\n'.join(linhasXml)+'\n</xml>'

if DEBUG: print("Extraindo dados...")
linhas = xmltodict.parse(linhasXml)['xml']['linha']
for linha in linhas:
    linha['numero'], linha['nome-linha'] = linha.pop('@label').split(' - ', 1)
    print(len(linha['viagens']), linha['nome-linha'])
    for viagem in linha['viagens']:
        viagem['id'] = viagem.pop('@value')
        nome = viagem.pop('#text').split(' - ')
        viagem['nome-viagem'] = ' '.join(nome[:-1])
        viagem['direcao'] = nome[-1].lower()
        print('\t' + viagem['direcao'] + '\t', viagem['nome-viagem'])

# pprint(linhas[:10])
print(len(linhas), 'linhas')
# exit()

if __name__ == '__main__':
    pass
