DEBUG = True
import json, re, threading, urllib, xmltodict
from pprint import pprint
from time import time
from multiprocessing.pool import ThreadPool as Pool

inicio = time()
API_CITTAMOBI = 'http://api.plataforma.cittati.com.br/m3p/js'
URL_LINHAS = 'http://info.plataforma.cittati.com.br/m3p/embedded/predictionMap'
ID_SMTT = 415
i = 0


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
            if linha['numero'] == '797': print(viagem['id'])
            nome = viagem.pop('#text')
            viagem['direcao'] = 'volta' if 'VOLTA' in nome else 'ida'
            viagem.update({
                'nome' : re.sub(r'(\ *\-\ *)(IDA|VOLTA)', '', nome),
                'linha' : linha['nome'],
                'numero' : linha['numero'],})
            viagens[viagem['id']] = dict(viagem)
    totalViagens = len(viagens)
    i = 0
    def worker(viagem):
        global i
        try:
            i += 1
            viagem['veiculos'] = pegaVeiculos(viagem['id'])
            print("%.1f%%\t%d veiculos na linha:" % (100*i/totalViagens, len(viagem['veiculos'])), viagem['nome'])
        except Exception as e:
            print(e)
            raise

    pool_size = 50  # your "parallelness"
    pool = Pool(pool_size)
    if DEBUG:
        for n,v in viagens.items():
            pool.apply_async(worker, (v,))
            # v['veiculos'] = pegaVeiculos(n)
        pool.close()
        pool.join()

    if DEBUG: print('%d linhas, %d viagens (em %d s)' % (len(linhas), len(viagens), time()-inicio))
    return viagens

def pegaVeiculos(idViagem):
    URL_VEICULOS = API_CITTAMOBI + '/vehicles/service/' + str(idViagem)
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
    return veiculos


if __name__ == '__main__':
    linhas = pegaLinhas()
    # pprint(linhas)
    # pprint(pegaVeiculos(22514))
