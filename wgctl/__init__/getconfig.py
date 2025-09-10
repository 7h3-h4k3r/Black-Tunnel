import json

def getconf():
    f = open('../../env.json')
    config = f.read()
    json_data = json.loads(config)
    return json_data