#include "../include/utils.h"

FILE* safe_fopen(const char* path, const char* mode) {
    FILE* file = fopen(path, mode);
    if (!file) {
        perror("Error opening file");
        exit(EXIT_FAILURE);
    }
    return file;
}