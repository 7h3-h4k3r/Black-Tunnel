#!/usr/bin/env python3
import subprocess
import sys
from  __init__.getconfig import getconf
def start_service(interface):
    config = getconf()
    try:
        subprocess.run(
            ['sudo wg-quick down ' +interface],
            cwd=config['wireguard_path'],
            shell=True,
            check=True
        )
    except subprocess.CalledProcessError:
        exit(-1)
if __name__ == '__main__':
    try:
        if len(sys.argv[1:]) >=1:
            args = sys.argv[1:]
            start_service(args[0])
        else:
            exit(-2)
        
    except:
       exit(-1)
    
        