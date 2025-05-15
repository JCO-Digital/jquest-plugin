import { readFileSync, writeFileSync } from "fs";

try {
  const pluginFileName = "jquest-plugin.php";
  const pack = JSON.parse(readFileSync("package.json"));
  const baseFile = readFileSync(pluginFileName);
  const baseString = baseFile
    .toString()
    .replace(/^(.*)Version:.*$/m, `$1Version: ${pack.version}`);
  writeFileSync(pluginFileName, baseString);

  const readmeFile = readFileSync("readme.txt");
  const readmeString = readmeFile
    .toString()
    .replace(/^(.*)Stable tag:.*$/m, `$1Stable tag: ${pack.version}`);
  writeFileSync("readme.txt", readmeString);
} catch (error) {
  console.error(error);
}