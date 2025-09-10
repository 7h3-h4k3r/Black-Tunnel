import sys
from __init__.wgctl import wgctl
if __name__ == '__main__':
    if len(sys.argv[1:]) > 1:
        wgctl_ = wgctl(sys.argv[1:])
        wgctl_.genConf()
        sys.exit(wgctl_.route)
    else:
        sys.exit(-1)