DEBUG = True
import json, re, threading, urllib, xmltodict
from pprint import pprint
from time import time
from multiprocessing.pool import ThreadPool as Pool
from sys import stdout

inicio = time()
API_CITTAMOBI = 'http://api.plataforma.cittati.com.br/m3p/js'
URL_LINHAS = 'http://info.plataforma.cittati.com.br/m3p/embedded/predictionMap'
ID_SMTT = 415
i = 0
totalVeiculos = 0


def pegaLinhas():
    def pegaLinhasXml():
        linhas = urllib.request.urlopen(URL_LINHAS)
        sessao = re.findall(r'[0-9A-F]{32}', linhas.geturl())[0]
        linhas = urllib.request.urlopen(URL_LINHAS
        +';jsessionid=%s?0-1.IBehaviorListener.0-mapPanel&out=false&ta=%d'
        % (sessao, ID_SMTT))
        return str(linhas.read().decode('utf8'))

    linhasXml = []
    correcoes = {
        r'(--)':'',
        r'\n\t?':'#',
        r'optgroup':'linha',
        r'option':'viagens',
        r'\( +':'(',
        r'\) +':')',
        r'( +[xX/] +)':' / ',
    }

    while len(linhasXml) == 0:
        if DEBUG: print("Carregando linhas...")
        linhasXml = pegaLinhasXml()
        for erro, correcao in correcoes.items():
            linhasXml = re.sub(erro, correcao, linhasXml)
        filtro = re.compile(r'<linha.*/linha>')
        linhasXml = re.findall(filtro,linhasXml)
    if DEBUG: print("Tratando dados...")
    linhasXml = linhasXml[0].split('#')
    linhasXml = '<xml>\n'+'\n'.join(linhasXml)+'\n</xml>'

    if DEBUG: print("Extraindo dados...")
    linhas = xmltodict.parse(linhasXml)['xml']['linha']
    viagens = {}
    for linha in linhas:
        linha['numero'] = re.findall(r'\d{2,}', linha['@label'])[0]
        linha['nome'] = linha.pop('@label')
        # print(len(linha['viagens']), linha['nome'])
        for viagem in linha['viagens']:
            viagem['id'] = viagem.pop('@value')
            nome = viagem.pop('#text')
            viagem['direcao'] = 'volta' if 'VOLTA' in nome else 'ida'
            viagem.update({
                'nome' : re.sub(r'(\ *\-\ *)(IDA|VOLTA)', '', nome),
                'linha' : linha['nome'],
                'numero' : linha['numero'],})
            viagens[viagem['id']] = dict(viagem)
    totalViagens = len(viagens)
    def worker(viagem):
        while True:
            try:
                global i, totalVeiculos
                i += 1
                pegaVeiculos(viagem)
                numVeiculos = len(viagem['veiculos'])
                totalVeiculos += numVeiculos
                status = '%.1f%%\t' % (100*i / totalViagens)
                status += str(numVeiculos)
                status += ' veículos na linha '
                status += '%s ' % ('🡠🡢'[viagem['direcao'] == 'ida'])
                status += '%s ' % viagem['nome']
                stdout.write(status.ljust(90) + ('\r' if numVeiculos == 0 else '\n'))
                break
            except Exception as e:
                print(e, ' (Tentando novamente...)')
                continue

    pool_size = 20
    pool = Pool(pool_size)
    if DEBUG:
        pool.map_async(worker, viagens.values())
        pool.close()
        pool.join()

    if DEBUG:
        relatorio = '%d linhas, '   % len(linhas)
        relatorio += '%d viagens, ' % totalViagens
        relatorio += '%d veículos agora ' % totalVeiculos
        relatorio += '(em %d s)' % (time()-inicio)
        print(relatorio.ljust(90))
    return viagens

def pegaVeiculos(viagem):
    URL_VEICULOS = API_CITTAMOBI + '/vehicles/service/' + viagem['id']
    veiculos = urllib.request.urlopen(URL_VEICULOS)
    veiculos = str(veiculos.read().decode('utf8'))
    veiculos = json.loads(veiculos)
    traducoes = {
        'bearing': 'angulo',
        'lat': 'lat',
        'lng': 'lng',
        'plate': 'placa',
        'prefix': 'numero',
        'ts': 'datahora',
    }
    for v in veiculos:
        for original, novo in traducoes.items():
            v[novo] = v.pop(original)
    viagem['veiculos'] = veiculos


if __name__ == '__main__':
    linhas = pegaLinhas()
    # pprint(linhas)
    # pprint(pegaVeiculos(22514))
