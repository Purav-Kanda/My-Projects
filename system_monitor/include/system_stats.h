#pragma once

typedef struct {
    float cpu_usage;
    float mem_usage;
} SystemStats;

// Core functions
SystemStats get_system_stats();
void list_top_processes(int n);
void log_system_stats(const char* filename, float cpu, float mem);