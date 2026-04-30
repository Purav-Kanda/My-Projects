CC=gcc
CFLAGS=-Wall -Wextra -Iinclude
SRC=src/monitor.c src/system_stats.c src/utils.c
BIN=monitor

all: $(BIN)

$(BIN): $(SRC)
	$(CC) $(CFLAGS) -o $@ $^

clean:
	rm -f $(BIN)