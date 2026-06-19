#pragma once
#include <stdbool.h>

typedef struct {
    float cpu_usage;
    float mem_usage;
    bool  valid;   // false if either /proc read failed
} SystemStats;

SystemStats get_system_stats();
void list_top_processes(int n);
void log_system_stats(const char* filename, float cpu, float mem);