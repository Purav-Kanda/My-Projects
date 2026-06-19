#include "../include/utils.h"

FILE* safe_fopen(const char* path, const char* mode) {
    FILE* file = fopen(path, mode);
    if (!file) {
        perror("Error opening file");
        return NULL;   // let the caller decide — don't kill the whole process
    }
    return file;
}