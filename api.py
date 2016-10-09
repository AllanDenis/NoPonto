DEBUG = True
import urllib
import re
import xmltodict
from pprint import pprint

URL_LINHAS = 'http://info.plataforma.cittati.com.br/m3p/embedded/predictionMap'
ID_SMTT = 415

def pegaLinhasXml():
    linhas = urllib.request.urlopen(URL_LINHAS)
    sessao = re.findall(r'[0-9A-F]{32}', linhas.geturl())[0]
    linhas = urllib.request.urlopen(URL_LINHAS
                                    +';jsessionid=%s?0-1.IBehaviorListener.0-mapPanel&out=false&ta=%d'
                                     % (sessao, ID_SMTT))
    return str(linhas.read())


def linhas():
    if DEBUG: print("Carregando linhas...")
    linhasXml = pegaLinhasXml()

    if DEBUG: print("Tratando dados...")
    linhasXml = re.sub(r'(--)','',linhasXml)
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

    print(len(linhas), 'linhas')


if __name__ == '__main__':
    linhas()
