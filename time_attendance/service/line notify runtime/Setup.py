from cx_Freeze import setup, Executable

executables = [Executable("Start.py")]

options = {
    "build_exe": {
        "packages": ["pyodbc", "requests", "datetime", "time", "json", "logging", "os"],
        "include_files": ["config/"],
    },
}

setup(
    name="Line Notify Runtime",
    version="1.0",
    description="Line Notify Runtime",
    options=options,
    executables=executables
)
