#!/bin/bash
set -euo pipefail
ROOT="$(pwd)"
find "$ROOT" -type f -name '*.bak.*' -print0 | while IFS= read -r -d '' f; do
  orig="${f%%.bak.*}"
  if [ -f "$f" ]; then
    echo "Restoring: $f -> $orig"
    cp -v -- "$f" "$orig"
  fi
done
