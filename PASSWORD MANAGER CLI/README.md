Documentation for the System Monitor CLI Tool

Description: A small C application for Linux real-time system monitoring. shows the top processes and monitors CPU and memory usage.

Documents:

include/system_stats.h

declares functions and structures for system statistics.

Important functions include log_system_stats(), list_top_processes(), and get_system_stats().

include/utils.h

Helper roles

The safe_fopen() core function

The main program, src/monitor.c

manages CLI arguments

puts in place a live monitoring loop

Use:./monitor [-c] [-p N] [-l file.log]

src/system_stats.c

carries out core monitoring:

CPU percentage obtained from /proc/stat

/proc/meminfo's memory percentage

Listing processes using the ps command

CSV recording

src/utils.c

Error-handling and safe file operations

Instructions for Building:

Put together: create

Execute:./monitor

Make it clean.

CLI Preferences:
-c Display just CPU usage -p N Display the top N processes (by default, 5).
-l file Statistics are recorded in a CSV file.

Format of Output: === System Monitor ===
CPU Utilisation: XX.XX%
Memory Utilisation: XX.XX%
[Top procedures]

Technical Information:

utilises the Linux /proc filesystem

Rate of refresh: 1 second

Log format: timestamp, CPU percentage, and memory percentage

Dependencies:

GCC

Linux operating system

The standard C library

Handling Errors:

gracefully ends when a file error occurs.

Verifies CLI arguments

Use Case Examples:

A quick check of the system:./monitor

Long-term observation:./monitor -l stats.csv

Troubleshooting the process:./monitor -p 10