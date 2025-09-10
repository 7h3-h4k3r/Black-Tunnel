#include<stdio.h>
#include<stdlib.h>
#include<math.h>
#include<string.h>
void _cuts(char *source,int class_ip){
    int j = 0 , dot = 0;
    for(int i =0;i<strlen(source);i++){
        if(*(source+i)=='.'){
            dot++;
        }
        if(dot==class_ip){
            *(source+i)='\0';
            break;
        }
    }
}

void slice(char *source,char *dest,int start_i){
    int j=0;
    if(start_i==0){
        printf("[*] Error Cidr Notation is Not valied Ex: 10.20.0.1/24\n");
        exit(-1);
    }
    for (int i = start_i+1; i <source[i] != '\0'; i++) {
        dest[j++] = source[i];
    }
    dest[j] = '\0';
    
}
int slice_index(char *source) {
    int j =0;
    int start_i = 0;    
    while(*(source)!='\0'){
        if(*(source)=='/'){ 
            return start_i;
        }
        start_i++;
        source++;
    }
    return 0; 
}

void _class_C(char *ip,int host){
    int i,j;
    int _u_host=-1;
    if(host<=1){
        printf("[*] Error total Number of Hosts:1 Number of Usable Hosts:0 hmmmm........ 😒\n");
        exit(-1);
    }
    for(i=2;i<=255;i++){
         printf("%s.%d\n",ip,i);
        if(_u_host == host){
            exit(-1);
        }
        _u_host++;
    }
}
void _class_B(char *ip,int host){
    int i,j;
    int _u_host=-1;
    
    for(i=0;i<=255;i++){
            for(j=2;j<255;j++){
                printf("%s.%d.%d\n",ip,i,j);
                _u_host++;
            }
            if(_u_host==host){
                break;
            }
        }
    }
void _class_A(char *ip,int host){
    int i,j,k;
    int _u_host=-1;
    for(i=0;i<=255;i++){
            for(j=0;j<=255;j++){
                for (k=2 ;k<=255;k++){
                    printf("%s.%d.%d.%d\n",ip,i,j,k);
                    _u_host++;
                    if(_u_host==host){
                        exit(-1);
                    }
                }
            }
                
    }
}



void _print_cidr(char *cidr){
    printf("%s",cidr);
}
int _classip(int cidr){
    if(8<=cidr && cidr<=15)
    {
        return 1;
    }
    else if(16<=cidr && cidr<=23)
    {
        return 2;
    }
    else if(24<=cidr && cidr<=32){
        return 3; 
    }

    return 0;

}
int _cide_int(char *subnet){
    return atoi(subnet);
}

int __usable_host(int subet){
    int uhost = pow(2,(32-subet));
    return uhost-2;
}
int main ( int argc ,char *argv[])
{
    char snet[10];
    int class_ip=0;
    if(argc==2){
        slice(argv[1],snet,slice_index(argv[1]));
        class_ip= _classip(_cide_int(snet));
        _cuts(argv[1],class_ip);
    }else{
        printf("[*] Error : Argurment Invalid\n ");
        exit(-1);
    }
    // printf("%s\n",argv[1]);
    // printf("%d\n",class_ip);
    switch(class_ip){
        case 1:
            _class_A(argv[1],__usable_host(_cide_int(snet)));
            break;
        case 2:
            _class_B(argv[1],__usable_host(_cide_int(snet)));
            break;
        case 3:
            _class_C(argv[1],__usable_host(_cide_int(snet)));
            break;

        default:
            printf("[*] Error : Cidr Notaion is Wrong or Host are TOO lengthy..\n");
            exit(-1);
            break;
    }
    return 0;
   
    
    
    
}
