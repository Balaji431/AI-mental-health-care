package com.example.mindcare.ui.theme

import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.ColorScheme
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable

private val LightColors = lightColorScheme(
    primary = PurplePrimary,
    secondary = PurpleSecondary,
    tertiary = BlueAccent,
    background = SurfaceLight,
    surface = CardLight,
    onPrimary = ColorWhite,
    onSecondary = ColorWhite,
    onTertiary = ColorWhite,
    onBackground = OnSurfaceLight,
    onSurface = OnSurfaceLight
)

private val DarkColors = darkColorScheme(
    primary = PurpleSecondary,
    secondary = PurplePrimary,
    tertiary = BlueAccent,
    background = SurfaceDark,
    surface = CardDark,
    onPrimary = ColorWhite,
    onSecondary = ColorWhite,
    onTertiary = ColorWhite,
    onBackground = OnSurfaceDark,
    onSurface = OnSurfaceDark
)

val ColorWhite = androidx.compose.ui.graphics.Color(0xFFFFFFFF)

@Composable
fun MindCareTheme(
    darkTheme: Boolean = isSystemInDarkTheme(),
    colorScheme: ColorScheme = if (darkTheme) DarkColors else LightColors,
    content: @Composable () -> Unit
) {
    MaterialTheme(
        colorScheme = colorScheme,
        typography = MindCareTypography,
        content = content
    )
}


