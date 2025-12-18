package com.example.mindcare.ui.components

import androidx.compose.animation.AnimatedVisibility
import androidx.compose.foundation.layout.height
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Assessment
import androidx.compose.material.icons.filled.Book
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Spa
import androidx.compose.material3.Icon
import androidx.compose.material3.NavigationBar
import androidx.compose.material3.NavigationBarItem
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.navigation.NavDestination
import com.example.mindcare.navigation.BottomNavScreens
import com.example.mindcare.navigation.Screen

@Composable
fun BottomNavBar(
    items: List<Screen>,
    currentDestination: NavDestination?,
    onItemSelected: (Screen) -> Unit
) {
    AnimatedVisibility(visible = true) {
        NavigationBar(
            modifier = Modifier.height(70.dp)
        ) {
            items.forEach { screen ->
                val selected = currentDestination?.route == screen.route
                NavigationBarItem(
                    selected = selected,
                    onClick = { onItemSelected(screen) },
                    icon = {
                        Icon(
                            imageVector = when (screen) {
                                Screen.Home -> Icons.Default.Home
                                Screen.Insights -> Icons.Default.Assessment
                                Screen.Journal -> Icons.Default.Book
                                Screen.Tools -> Icons.Default.Spa
                                Screen.Profile -> Icons.Default.Person
                                else -> Icons.Default.Home
                            },
                            contentDescription = null
                        )
                    },
                    label = {
                        Text(
                            text = when (screen) {
                                Screen.Home -> "Home"
                                Screen.Insights -> "Insights"
                                Screen.Journal -> "Journal"
                                Screen.Tools -> "Tools"
                                Screen.Profile -> "Profile"
                                else -> ""
                            }
                        )
                    }
                )
            }
        }
    }
}


