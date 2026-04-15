<script>
  (function () {
    const modeKey = 'adminThemeMode';
    const legacyModeKey = 'adminTheme';
    const presetKey = 'adminThemePreset';
    const allowedModes = ['light', 'dark'];
    const allowedPresets = ['ocean', 'emerald', 'sunset', 'plum'];

    let mode = 'light';
    let preset = 'ocean';

    try {
      const savedMode = window.localStorage.getItem(modeKey);
      const legacyMode = window.localStorage.getItem(legacyModeKey);
      const savedPreset = window.localStorage.getItem(presetKey);

      if (allowedModes.includes(savedMode)) {
        mode = savedMode;
      } else if (allowedModes.includes(legacyMode)) {
        mode = legacyMode;
      }

      if (allowedPresets.includes(savedPreset)) {
        preset = savedPreset;
      }
    } catch (error) {
      mode = 'light';
      preset = 'ocean';
    }

    document.documentElement.setAttribute('data-admin-mode', mode);
    document.documentElement.setAttribute('data-admin-theme', preset);
    document.documentElement.classList.toggle('dark-mode', mode === 'dark');
  })();
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css2?family=Merienda:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/common.css?v=<?php echo filemtime('css/common.css'); ?>">
<link rel="stylesheet" href="css/layout.css?v=<?php echo filemtime('css/layout.css'); ?>">
<link rel="stylesheet" href="css/admin-theme.css?v=<?php echo filemtime('css/admin-theme.css'); ?>">
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
