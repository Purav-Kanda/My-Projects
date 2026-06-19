#include "../include/system_stats.h"
#include "../include/utils.h"
#include <stdlib.h>  // system()
#include <stdio.h>   // snprintf()
#include <time.h>    // time()

SystemStats get_system_stats() {
    SystemStats stats = {0};
    
    // 1. Parse CPU stats (renamed 'system' to 'cpu_system')
    FILE* stat_file = safe_fopen("/proc/stat", "r");
    unsigned long user, nice, cpu_system, idle;
    fscanf(stat_file, "cpu %lu %lu %lu %lu", &user, &nice, &cpu_system, &idle);
    fclose(stat_file);

    unsigned long total = user + nice + cpu_system + idle;
    stats.cpu_usage = (total > 0) ? 
        (user + nice + cpu_system) * 100.0f / total : 0;

    // 2. Parse Memory stats
    FILE* mem_file = safe_fopen("/proc/meminfo", "r");
    unsigned long mem_total = 0, mem_available = 0;
    char line[256];
    while (fgets(line, sizeof(line), mem_file)) {
        if (!mem_total) sscanf(line, "MemTotal: %lu kB", &mem_total);
        if (!mem_available) sscanf(line, "MemAvailable: %lu kB", &mem_available);
    }
    fclose(mem_file);
    stats.mem_usage = (mem_total > 0) ? 
        (mem_total - mem_available) * 100.0f / mem_total : 0;

    return stats;
}

void list_top_processes(int n) {
    char command[256];
    snprintf(command, sizeof(command), 
        "ps -eo pid,%%cpu,%%mem,comm --sort=-%%cpu | head -n %d", n + 1);
    system(command);
}

void log_system_stats(const char* filename, float cpu, float mem) {
    FILE* log = fopen(filename, "a");
    if (log) {
        fprintf(log, "%ld,%.2f,%.2f\n", time(NULL), cpu, mem);
        fclose(log);
    }
}