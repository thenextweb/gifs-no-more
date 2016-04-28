#!/bin/bash

set -eu

if (( $# != 1 )); then
    echo "Converts MP4 to a OGV file."
    echo 1>&2
    echo "Usage: mp42ogv ANIMATED-GIF" 1>&2

    exit 1
fi

hash ffmpeg 2>/dev/null || {
    echo >&2 "Couldn't find ffmpeg. It MUST be installed.  Aborting."; \
    exit 1;
}

INFILE="${1}"
OUTFILE="${INFILE%.gif}.ogv"
# Stick the .mp4 in the same directory as the original .gif, regardless of
# current directory
OUTFILE="$(dirname -- "${OUTFILE}")/$(basename -- "${OUTFILE}")"

ffmpeg -i "${INFILE}" -c:v libtheora -an -movflags +faststart -vf 'scale=-2:ih' -pix_fmt yuv420p "${OUTFILE}"
