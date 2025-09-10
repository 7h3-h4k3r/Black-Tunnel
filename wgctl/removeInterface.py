#!/usr/bin/env python3
import subprocess
import sys
from  __init__.getconfig import getconf
config = getconf()
def down_service(interface):
    global config 
    try:
        subprocess.run(
            ['sudo wg-quick down ' +interface],
            cwd=config['wireguard_path'],
            shell=True,
            check=True
        )
    except subprocess.CalledProcessError:
        pass
def rmFolder(interface):
    global config
    config = getconf()
    try:
        subprocess.run(
            ['rm -rf ' +interface+' && rm '+interface+'.conf'],
            cwd=config['wireguard_path'],
            shell=True,
            check=True
        )
    except subprocess.CalledProcessError:
        return -1
if __name__ == '__main__':
  
    if len(sys.argv[1:]) >=1:
        args = sys.argv[1:]
        down_service(args[0])
        rmFolder(args[0])
    else:
         exit(-2)
        
