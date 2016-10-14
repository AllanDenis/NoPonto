import api, unittest
from urllib.request import urlopen
from multiprocessing.pool import ThreadPool

class TestApi(unittest.TestCase):
    """Testa se o servidor responde, se os links estão corretos, etc.
    """

    threads = 10
    viagensId = []
    pool = ThreadPool(threads)

    def setUp(self):
        pass

    def tearDown(self):
        pass


    def urlOk(self, url):
        "Retorna true se a url existir."
        try:
            return urlopen(url).code == 200
        except Exception as e:
            print('%s: %s' % (e, url))
            raise

    def emParalelo(self, func, args):
        "Executa paralelamente a função func com os argumentos em args."
        resultados = self.pool.map_async(func, args)
        self.pool.close()
        self.pool.join()
        return resultados


    def test_links(self):
        links = [
            api.URL_LINHAS,
            # api.API_CITTAMOBI,
        ]
        resultados = self.emParalelo(self.urlOk, links)
        self.assertTrue(resultados._success, 'Algum link não está OK.')


    def test_viagens(self):
        self.viagensId = api.pegaLinhas().keys()
        self.assertTrue(self.viagensId, 'Lista de viagens vazia.')




    # def test_isupper(self):
    #     self.assertTrue('FOO'.isupper())
    #     self.assertFalse('Foo'.isupper())
    #
    # def test_split(self):
    #     s = 'hello world'
    #     self.assertEqual(s.split(), ['hello', 'world'])
    #     # check that s.split fails when the separator is not a string
    #     with self.assertRaises(TypeError):
    #       s.split(2)

if __name__ == '__main__':
    unittest.main()
