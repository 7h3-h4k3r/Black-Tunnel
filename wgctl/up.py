#!/usr/bin/env python3
import subprocess
import sys
from  __init__.getconfig import getconf
def down_service(interface):
    config = getconf()
    try:
        subprocess.run(
            ['sudo wg-quick up ' +interface],
            cwd=config['wireguard_path'],
            shell=True,
            check=True
        )
    except subprocess.CalledProcessError:
        return -1

if __name__ == '__main__':
    try:
        if len(sys.argv[1:]) >=1:
            args = sys.argv[1:]
            down_service(args[0])
        else:
            exit(-2)
        
    except:
       exit(-1)
    
        