exe : calculate-ip.o
	gcc -o exe calculate-ip.o -lm

calculate-ip.o : calculate-ip.c
	gcc -c -o calculate-ip.o calculate-ip.c

clean:
	rm *.o 
	rm exe 
