#include "../include/system_stats.h"
#include <unistd.h>   // For getopt(), sleep()
#include <stdio.h>    // For printf()
#include <stdlib.h>   // For exit(), EXIT_FAILURE

int main(int argc, char *argv[]) {
    int show_processes = 5;  // Default: show 5 processes
    int cpu_only = 0;        // Default: show CPU and memory
    char *log_file = NULL;   // Default: no logging

    // Parse command-line options
    int opt;
    while ((opt = getopt(argc, argv, "cp:l:")) != -1) {
        switch (opt) {
            case 'c':
                cpu_only = 1;
                break;
            case 'p':
                show_processes = atoi(optarg);  // Convert string to integer
                break;
            case 'l':
                log_file = optarg;  // Store log filename
                break;
            default:
                fprintf(stderr, "Usage: %s [-c] [-p N] [-l filename.log]\n", argv[0]);
                fprintf(stderr, "Options:\n");
                fprintf(stderr, "  -c\t\tShow CPU usage only\n");
                fprintf(stderr, "  -p N\t\tShow top N processes\n");
                fprintf(stderr, "  -l filename\tLog stats to file\n");
                exit(EXIT_FAILURE);
        }
    }

    // Main monitoring loop
    while (1) {
        SystemStats stats = get_system_stats();
        
        // Clear screen (ANSI escape codes)
        printf("\033[2J\033[H");  
        
        printf("=== System Monitor ===\n");
        printf("CPU Usage: %.2f%%", stats.cpu_usage);
        
        if (!cpu_only) {
            printf(", Memory Usage: %.2f%%\n", stats.mem_usage);
            list_top_processes(show_processes);
        } else {
            printf("\n");  // Newline if only showing CPU
        }

        // Log to file if enabled
        if (log_file) {
            log_system_stats(log_file, stats.cpu_usage, stats.mem_usage);
        }

        sleep(1);  // Refresh every second
    }

    return 0;
}