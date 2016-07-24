from weppy import App
from weppy.dal import DAL, Model, Field

def test_banco():
    """Testa se o banco de dados está funcionando normalmente."""
    app = App(__name__)
    app.config_from_yaml('db.yml', 'db')
    db = DAL(app)
