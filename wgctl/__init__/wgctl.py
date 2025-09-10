import os
import subprocess
class wgctl:

    def __init__(self,args):
        self.args  = args 
        self.basecase = '/etc/wireguard'
        self.keycase = self.basecase+'/'+args[0]
        self.route =None
        self.keyname = ['privatekey','publickey']

    def inFile(self):
        return os.path.exists(self.keycase)

    def genFolder(self):
        if self.inFile():
            return False
        os.mkdir(self.keycase)
    
    def genKey(self):
        if self.genFolder()!= False:
            if os.path.exists(self.keycase):
                subprocess.run(
                    ['wg genkey | tee '+self.keyname[0]+' | wg pubkey > '+self.keyname[1]],
                    cwd=self.keycase,
                    shell=True,
                    check=True
                )
                i=0
                while i<=1:
                    with open(self.keycase+'/'+self.keyname[i],'r') as file:
                        self.keyname.insert(i,file.read().strip())
                        del self.keyname[i+1]
                    i+=1
                return True
                        
            else:
                return False
        else:
            self.route = False
        
    def getIProute(self):
        result =subprocess.run(
            ['ip route list default'],
            capture_output=True,
            shell=True
        )
        return result.stdout.decode().strip().split()

    def setDevice(self):
        parts = self.getIProute()
        self.route = parts[parts.index("dev") + 1] if "dev" in parts else None
    


    def genServerConf(self):
        self.setDevice()
        config = f"""[Interface]
    PrivateKey = {self.keyname[0]}
    ListenPort = {int(self.args[2])}
    Address = {self.args[1]}
    PostUp = iptables -A FORWARD -i %i -j ACCEPT; iptables -t nat -A POSTROUTING -o {self.route} -j MASQUERADE
    PostDown = iptables -D FORWARD -i %i -j ACCEPT; iptables -t nat -D POSTROUTING -o {self.route} -j MASQUERADE"""
        return config.strip()
    

    def genConf(self):
        genKey_ouput = self.genKey()
        if genKey_ouput:
            conf = self.genServerConf()
            config_file = self.args[0]+'.conf'
            with open(self.keycase+'/../'+config_file,'w') as file:
                file.write(conf)
                self.route = True
            file.close()
        else:  
            self.route = False

        
        